<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Member;
use App\Models\User;
use App\Data\Value\Account\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $districts = District::with('city')->get();
        return response()->json([
            'success' => true,
            'districts' => $districts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Member::class);

        $existingUser = User::where('username', $request->username)->first();

        $rules = [
            'username' => 'required|string|max:50',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'district_id' => 'required|integer',
        ];

        if ($existingUser) {
            $rules['username'] = 'required|unique:members,username';
        } else {
            $rules['username'] = 'required|string|max:50|unique:users,username';
            $rules['email'] = 'required|string|email|max:255|unique:users,email';
            $rules['password_hashed'] = 'required|string|min:8';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {

            if ($existingUser) {
                $existingUser->update([
                    'role' => Role::MEMBER->value
                ]);
            } else {
                User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password_hashed' => Hash::make($request->password),
                    'role' => Role::MEMBER->value,
                    'status' => 'active',
                ]);
            }

            Member::create([
                'username' => $request->username,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'district_id' => $request->district_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Member berhasil ditambahkan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($username)
    {
        $member = Member::with('user')->where('username', $username)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'member' => $member
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($username)
    {
        $member = Member::with('user')->where('username', $username)->first();

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Member tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'member' => $member
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $username)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'district_id' => 'required|integer',
        ]);

        $member = Member::where('username', $username)->firstOrFail();

        $this->authorize('update', $member);

        $member->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'address' => $request->address,
            'district_id' => $request->district_id,
        ]);

        if ($request->has('email')) {
            $member->user()->update([
                'email' => $request->email
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Member berhasil diperbarui!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($username)
    {
        DB::beginTransaction();
        try {
            $user = User::where('username', $username)->firstOrFail();

            Member::where('username', $username)->delete();
            $this->authorize('delete', $user);
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Member berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fetchmembers()
    {
        // Mengambil data member beserta relasi user (untuk email/status) jika diperlukan
        $members = Member::with(['user'])->get();

        return response()->json([
            'success' => true,
            'data' => $members,
        ]);
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
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
        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username'        => 'required|string|max:50|unique:users,username',
            'email'           => 'required|string|email|max:100|unique:users,email',
            'password_hashed' => 'required|string|min:8',
            'phone_number'    => 'nullable|string|max:20',
            'role'            => 'required|in:member,doctor,facility_admin,admin',
            'status'          => 'required|in:active,suspended',
        ]);

        try {
            User::create([
                'username'        => $request->username,
                'email'           => $request->email,
                'password_hashed' => Hash::make($request->password_hashed),
                'phone_number'    => $request->phone_number,
                'role'            => $request->role,
                'status'          => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data User berhasil ditambahkan!'
            ]);

        } catch (\Exception $e) {
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
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user'    => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user'    => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        $request->validate([            
            'email'           => 'required|string|email|max:100|unique:users,email,' . $username . ',username',
            'phone_number'    => 'nullable|string|max:20',
            'role'            => 'required|in:member,doctor,facility_admin,admin',
            'status'          => 'required|in:active,suspended',
            'password_hashed' => 'nullable|string|min:8',
        ]);

        try {            
            $updateData = [
                'email'        => $request->email,
                'phone_number' => $request->phone_number,
                'role'         => $request->role,
                'status'       => $request->status,
            ];
            
            if ($request->filled('password_hashed')) {
                $updateData['password_hashed'] = Hash::make($request->password_hashed);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Data User berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($username)
    {
        DB::beginTransaction();
        try {
            $user = User::where('username', $username)->firstOrFail();                        

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data User berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fetchUsers()
    {        
        $users = User::all();

        return response()->json([
            'success' => true,
            'data'    => $users,
        ]);
    }
}

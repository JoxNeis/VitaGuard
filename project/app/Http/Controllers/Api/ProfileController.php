<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Data\Value\Account\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function fetch()
    {
        $user = Auth::user();

        $profileData = match ($user->role) {
            Role::DOCTOR->value => $user->doctor,
            Role::MEMBER->value => $user->member,
            default => null,
        };

        return response()->json([
            'success' => true,
            'user' => $user,
            'profileData' => $profileData,
        ]);
    }

    public function update(Request $request)
    {
        return match (Auth::user()->role) {
            Role::MEMBER->value => $this->updateMemberProfile($request),
            Role::DOCTOR->value => $this->updateDoctorProfile($request),
            default => response()->json([
                'success' => false,
                'message' => 'Role tidak dikenali.'
            ], 403),
        };
    }

    private function updateMemberProfile(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->username . ',username',
            'phone_number' => 'nullable|string|max:20',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
        ]);

        DB::beginTransaction();

        try {
            $user->update([
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password_hashed' => Hash::make($request->password),
                ]);
            }

            $member->update([
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
                'message' => 'Profil berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateDoctorProfile(Request $request)
    {
        $user = Auth::user();
        $doctor = $user->doctor;

        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->username . ',username',
            'phone_number' => 'required|string|max:20',
            'prefix_name' => 'nullable|string|max:20',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'suffix_name' => 'nullable|string|max:100',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
        ]);

        DB::beginTransaction();

        try {
            $user->update([
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password_hashed' => Hash::make($request->password),
                ]);
            }

            $doctor->update([
                'prefix_name' => $request->prefix_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'suffix_name' => $request->suffix_name,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'district_id' => $request->district_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }
}
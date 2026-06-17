<?php

namespace App\Http\Controllers\APi;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use App\Models\DoctorSpecialty;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $doctors = Doctor::with('specialties.specialty')->get();
        $specialties = Specialty::all();
        return view('pages.doctors.index', compact('doctors', 'specialties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $districts = District::all();
        $specialties = Specialty::all();

        return response()->json([
            'success' => true,
            'districts' => $districts,
            'specialties' => $specialties,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|alpha_num|unique:users,username|unique:doctors,username',
            'password' => 'required|string|min:6',
            'email' => 'required|email|max:255|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'specialties' => 'required|array',
        ], [
            'username.unique' => 'Username sudah digunakan oleh pengguna lain.',
            'username.alpha_num' => 'Username hanya boleh berisi huruf dan angka tanpa spasi.'
        ]);

        DB::beginTransaction();

        try {
            User::create([
                'username' => $request->username,
                'password_hashed' => Hash::make($request->password),
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'status' => 'active',
                'role' => 'doctor',
            ]);

            Doctor::create([
                'username' => $request->username,
                'prefix_name' => $request->prefix_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'suffix_name' => $request->suffix_name,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'district_id' => $request->district_id,
            ]);

            foreach ($request->specialties as $specialtyId) {
                DoctorSpecialty::create([
                    'doctor' => $request->username,
                    'specialty_id' => $specialtyId
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data dokter dan akun berhasil dibuat!'
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($username)
    {
        $doctor = Doctor::with(['user', 'specialties'])->where('username', $username)->firstOrFail();
        $districts = District::all();
        $specialties = Specialty::all();

        return response()->json([
            'success' => true,
            'doctor' => $doctor,
            'districts' => $districts,
            'specialties' => $specialties,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $username)
    {
        $doctor = Doctor::where('username', $username)->firstOrFail();
        $user = User::where('username', $username)->firstOrFail();
        
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->username . ',username',
            'phone_number' => 'required|string|max:20',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'address' => 'required|string|max:255',
            'district_id' => 'required|exists:districts,id',
            'specialties' => 'required|array',
        ]);

        DB::beginTransaction();

        try {            
            $user->update([
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);
           
            if ($request->filled('password')) {
                $user->update([
                    'password_hashed' => Hash::make($request->password)
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
            
            DoctorSpecialty::where('doctor', $username)->delete();
            foreach ($request->specialties as $specialtyId) {
                DoctorSpecialty::create([
                    'doctor' => $username,
                    'specialty_id' => $specialtyId
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data dokter berhasil diperbarui!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function fetchDoctors()
    {
        $doctors = Doctor::with('specialties.specialty')
            ->orderBy('username', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $doctors
        ]);
    }
}

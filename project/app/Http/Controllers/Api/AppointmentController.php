<?php

// namespace App\Http\Controllers\Api;
namespace App\Http\Controllers\Api;


use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Specialty;
use App\Models\Consultation;
use App\Models\OnlineSession;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index()
    {
        $doctors = Doctor::all();
        $currentPatient = Auth::check() ? Auth::user()->username : null;

        $doctors = Doctor::with(['specialties', 'schedules' => function($query) use ($currentPatient) {
            if ($currentPatient) {
                $query->with(['appointments' => function($q) use ($currentPatient) {
                    $q->where('patient', $currentPatient)->where('status', '!=', 'cancelled');
                }]);
            }
        }])->get();

        foreach ($doctors as $doctor) {
            $hasBooked = false;
            if ($currentPatient) {
                $hasBooked = Appointment::where('patient', $currentPatient)
                    ->whereHas('schedule', function ($q) use ($doctor) {
                        $q->where('doctor', $doctor->username);
                    })
                    ->where('status', '!=', 'cancelled')
                    ->exists();
            }
            $doctor->has_booked = $hasBooked;
            foreach ($doctor->schedules as $schedule) {
                $schedule->is_booked_by_user = $schedule->appointments->isNotEmpty();
            }
        }

        $specialties = Specialty::all();

        return view('pages.appointments.index', compact('doctors', 'specialties'));
    }
    public function member()
    {
        return view('pages.appointments.member');
    }

    public function doctor()
    {
        return view('pages.appointments.doctor');
    }


    public function create()
    {
    }

    public function fetchDoctorAppointments()
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role != 'doctor') {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $appointments = Appointment::whereHas('schedule', function($q) use ($user) {
                $q->where('doctor', $user->username);
            })
            ->with(['schedule', 'patient'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

            $data = $appointments->map(function ($appointment) {
                $patient = $appointment->patient;
                
                if (is_string($patient)) {
                    $member = \App\Models\Member::where('username', $patient)->first();
                    $patientName = $member ? $member->first_name . ' ' . $member->last_name : $patient;
                } else {
                    $patientName = $patient ? $patient->first_name . ' ' . $patient->last_name : 'Pasien tidak ditemukan';
                }

                return [
                    'id'            => $appointment->id,
                    'patient_name'  => $patientName,
                    'date'          => $appointment->date,
                    'time'          => $appointment->time,
                    'queue_order'   => $appointment->queue_order,
                    'status'        => $appointment->status,
                    'notes'         => $appointment->notes,
                ];
            });

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }


    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $appointment = Appointment::findOrFail($id);


        if ($user->role == 'doctor') {
            $schedule = $appointment->schedule;
            if (!$schedule || $schedule->doctor != $user->username) {
                return response()->json(['success' => false, 'message' => 'Unauthorized to update this appointment'], 403);
            }
        } elseif ($user->role != 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show'
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        if ($request->status === 'confirmed') {

            // Cek apakah consultation sudah pernah dibuat
            $existingConsultation = Consultation::where('patient', $appointment->patient)
                ->whereNull('end_time')
                ->first();

            if (!$existingConsultation) {

                $doctorUsername = $appointment->schedule->doctor;

                $existingConsultation = Consultation::where('patient', $appointment->patient)
                    ->whereHas('onlineSession', function ($q) use ($doctorUsername) {
                        $q->where('doctor', $doctorUsername);
                    })
                    ->whereNull('end_time')
                    ->first();

                if (!$existingConsultation) {

                    $onlineSession = OnlineSession::create([
                        'doctor' => $doctorUsername,
                        'start_time' => now(),
                        'end_time' => null,
                        'consultation_fee' => 0,
                        'description' => 'Appointment #' . $appointment->id,
                    ]);

                    Consultation::create([
                        'online_session_id' => $onlineSession->id,
                        'patient' => $appointment->patient,
                        'start_time' => now(),
                        'end_time' => null,
                        'notes' => $appointment->notes,
                        'paid_at' => null,
                    ]);
                }
            }
        }

        return response()->json(['success' => true, 'message' => 'Status updated']);
    }

    public function fetchAppointments()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            $appointments = Appointment::where('patient', $user->username)
                ->with(['schedule.doctorData'])
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->get();

            $data = $appointments->map(function ($appointment) {
                $doctor = $appointment->schedule->doctorData ?? null;
                
                return [
                    'id'            => $appointment->id,
                    'doctor_name'   => $doctor ? "dr. {$doctor->first_name} {$doctor->last_name}" : 'Dokter tidak ditemukan',
                    'date'          => $appointment->date,
                    'time'          => $appointment->time,
                    'queue_order'   => $appointment->queue_order,
                    'status'        => $appointment->status,
                    'notes'         => $appointment->notes,
                ];
            });

            return response()->json([
                'success' => true,
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu untuk melakukan booking.'
            ], 401);
        }

        $request->validate([
            'doctor_id'   => 'required|exists:doctors,username',
            'schedule_id' => 'required|exists:doctor_schedules,id',
            'date'        => 'required|date|after_or_equal:today',
            'notes'       => 'nullable|string|max:255',
        ]);

        $patientUsername = Auth::user()->username;

        $existingAppointment = Appointment::where('patient', $patientUsername)
            ->where('doctor_schedule_id', $request->schedule_id)
            ->whereDate('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan booking untuk jadwal dan tanggal ini.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $schedule = DoctorSchedule::findOrFail($request->schedule_id);

            $queueOrder = Appointment::where('doctor_schedule_id', $schedule->id)
                ->whereDate('date', $request->date)
                ->count() + 1;

            $appointment = Appointment::create([
                'patient'            => $patientUsername,
                'doctor_schedule_id' => $schedule->id,
                'date'               => $request->date,
                'time'               => $schedule->open_time,
                'queue_order'        => $queueOrder,
                'status'             => 'pending',
                'notes'              => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Booking jadwal konsultasi berhasil.',
                'queue_order'  => $appointment->queue_order,
                'appointment'  => $appointment,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data booking: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show(string $id)
    {

    }

    public function edit(string $id)
    {
    }

    public function update(Request $request, string $id)
    {
    }

    public function destroy(string $id)
    {
    }
}

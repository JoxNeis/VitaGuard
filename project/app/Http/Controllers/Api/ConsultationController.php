<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Member;
use App\Models\OnlineSession;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctors = Doctor::all();
        $currentPatient = Auth::check() ? Auth::user()->username : null;

        $doctors = Doctor::with([
            'specialties',
            'schedules' => function ($query) use ($currentPatient) {
                if ($currentPatient) {
                    $query->with([
                        'appointments' => function ($q) use ($currentPatient) {
                            $q->where('patient', $currentPatient)->where('status', '!=', 'cancelled');
                        }
                    ]);
                }
            }
        ])->get();

        foreach ($doctors as $doctor) {
            $hasBooked = false;
            $consultationId = null;

            if ($currentPatient) {
                $hasBooked = Appointment::where('patient', $currentPatient)
                    ->whereHas('schedule', function ($q) use ($doctor) {
                        $q->where('doctor', $doctor->username);
                    })
                    ->where('status', '!=', 'cancelled')
                    ->exists();


                if ($hasBooked) {
                    $consultation = Consultation::where('patient', $currentPatient)
                        ->whereHas('onlineSession', function ($q) use ($doctor) {
                            $q->where('doctor', $doctor->username);
                        })
                        ->whereNull('end_time')
                        ->first();
                    if ($consultation) {
                        $consultationId = $consultation->id;
                    }
                }
            }

            $doctor->has_booked = $hasBooked;
            $doctor->can_chat = $hasBooked;
            $doctor->consultation_id = $consultationId;
        }

        $specialties = Specialty::all();
        $schedules = DoctorSchedule::all();

        return view('pages.consultations.index', compact('doctors', 'specialties', 'schedules'));
    }


    public function indexSpecialties()
    {
        $doctors = Doctor::with(['specialties', 'schedules'])->get();
        $specialties = Specialty::all();

        return view('pages.consultations.index', compact('doctors', 'specialties'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::select('username', 'first_name', 'middle_name', 'last_name')->get();

        $onlineSessions = OnlineSession::with('doctorData')
            ->whereNull('deleted_at')
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'members' => $members,
            'online_sessions' => $onlineSessions,
        ]);
    }

    public function member()
    {
        return view('pages.consultations.member');
    }

    public function doctor()
    {
        return view('pages.consultations.doctor');
    }

    public function start(Doctor $doctor)
    {
        $patient = auth()->user()->username;

        $existing = Consultation::where('patient', $patient)
            ->whereHas('onlineSession', function ($q) use ($doctor) {
                $q->where('doctor', $doctor->username);
            })
            ->whereNull('end_time')
            ->first();

        if ($existing) {
            return redirect()->route('chat', $existing->id);
        }

        try {
            $consultation = null;
            DB::transaction(function () use ($patient, $doctor, &$consultation) {
                $onlineSession = OnlineSession::create([
                    'doctor' => $doctor->username,
                    'start_time' => now(),
                    'end_time' => null,
                    'consultation_fee' => 0,
                    'description' => 'Konsultasi chat langsung',
                ]);

                $consultation = Consultation::create([
                    'online_session_id' => $onlineSession->id,
                    'patient' => $patient,
                    'start_time' => now(),
                    'end_time' => null,
                    'notes' => null,
                    'paid_at' => null,
                ]);
            });

            if ($consultation) {
                return redirect()->route('chat', $consultation->id);
            }

            return redirect()->back()->with('error', 'Gagal membuat konsultasi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'online_session_id' => 'required|integer|exists:online_sessions,id',
            'patient' => 'required|string|exists:members,username',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'notes' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            Consultation::create([
                'online_session_id' => $request->online_session_id,
                'patient' => $request->patient,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes,
                'paid_at' => $request->paid_at,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Konsultasi berhasil ditambahkan!'
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
    public function show($id)
    {
        $consultation = Consultation::with([
            'patientData',
            'onlineSession.doctorData'
        ])->find($id);

        if (!$consultation) {
            return response()->json([
                'success' => false,
                'message' => 'Data Konsultasi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'consultation' => $consultation
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $consultation = Consultation::find($id);

        if (!$consultation) {
            return response()->json([
                'success' => false,
                'message' => 'Data Konsultasi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'consultation' => $consultation
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        $request->validate([
            'online_session_id' => 'required|integer|exists:online_sessions,id',
            'patient' => 'required|string|exists:members,username',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'notes' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $consultation->update([
                'online_session_id' => $request->online_session_id,
                'patient' => $request->patient,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes,
                'paid_at' => $request->paid_at,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Konsultasi berhasil diperbarui!'
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
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $consultation = Consultation::findOrFail($id);

            // Soft delete
            $consultation->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data Konsultasi berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function fetchConsultations()
    {
        $user = auth()->user();

        if ($user->role === 'doctor') {
            $sessionIds = OnlineSession::where('doctor', $user->username)->pluck('id');

            $consultations = Consultation::with('onlineSession.doctorData')
                ->whereIn('online_session_id', $sessionIds)
                ->orderBy('start_time', 'desc')
                ->get();
        } else {
            $consultations = Consultation::with('onlineSession.doctorData')
                ->where('patient', $user->username)
                ->orderBy('start_time', 'desc')
                ->get();
        }

        $data = $consultations->map(function ($c) {
            return [
                'id' => $c->id,
                'patient' => $c->patient,
                'doctor' => $c->onlineSession->doctorData ? $c->onlineSession->doctorData->first_name . ' ' . $c->onlineSession->doctorData->last_name : $c->onlineSession->doctor,
                'start_time' => $c->start_time ? $c->start_time->format('d M Y H:i') : '-',
                'end_time' => $c->end_time ? $c->end_time->format('d M Y H:i') : null,
                'notes' => $c->notes,
                'is_active' => is_null($c->end_time),
                'chat_url' => '/chat/' . $c->id,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function fetchAllConsultations()
    {
        $consultations = Consultation::with(['patientData', 'onlineSession.doctorData'])->get();

        return response()->json([
            'success' => true,
            'data' => $consultations,
        ]);
    }

    public function book(Request $request)
    {
        // Pengecekan Login
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu untuk melakukan booking.'
            ], 401);
        }

        $request->validate([
            'doctor_id' => 'required|exists:doctors,username',
            'schedule_id' => 'required|exists:doctor_schedules,id',
            'date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:255',
        ]);

        $patientUsername = Auth::user()->username;

        // Mencegah booking ganda
        $existingAppointment = \App\Models\Appointment::where('patient', $patientUsername)
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
            $schedule = \App\Models\DoctorSchedule::findOrFail($request->schedule_id);

            // Hitung nomor antrean
            $queueOrder = \App\Models\Appointment::where('doctor_schedule_id', $schedule->id)
                ->whereDate('date', $request->date)
                ->count() + 1;

            // 1. Buat Appointment
            $appointment = \App\Models\Appointment::create([
                'patient' => $patientUsername,
                'doctor_schedule_id' => $schedule->id,
                'date' => $request->date,
                'time' => $schedule->open_time,
                'queue_order' => $queueOrder,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // 2. Buat Online Session otomatis
            $onlineSession = \App\Models\OnlineSession::create([
                'doctor' => $request->doctor_id,
                'start_time' => now(),
                'end_time' => null,
                'consultation_fee' => 0,
                'description' => 'Appointment #' . $appointment->id,
            ]);

            // 3. Buat Consultation 
            Consultation::create([
                'online_session_id' => $onlineSession->id,
                'patient' => $patientUsername,
                'start_time' => now(),
                'end_time' => null,
                'notes' => $request->notes,
                'paid_at' => null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking jadwal konsultasi berhasil.',
                'queue_order' => $appointment->queue_order,
                'appointment' => $appointment,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data booking: ' . $e->getMessage()
            ], 500);
        }
    }
}

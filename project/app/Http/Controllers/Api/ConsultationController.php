<?php

namespace App\Http\Controllers\Api  ;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\Consultation;
use App\Models\OnlineSession;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

        $doctors = Doctor::with(['specialties', 'schedules' => function($query) use ($currentPatient) {
            if ($currentPatient) {
                $query->with(['appointments' => function($q) use ($currentPatient) {
                    $q->where('patient', $currentPatient)->where('status', '!=', 'cancelled');
                }]);
            }
        }])->get();

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

        return view('pages.consultations.index', compact('doctors', 'specialties'));
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
        //
    }

    public function member()
    {
        return view('pages.consultations.member');
    }

    public function doctor()
    {
        return view('pages.consultations.doctor');
    }

    /**
     * Store a newly created resource in storage.
     */
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

            $onlineSession = \App\Models\OnlineSession::create([
                'doctor'           => $request->doctor_id,
                'start_time'       => now(),
                'end_time'         => null,
                'consultation_fee' => 0,
                'description'      => 'Appointment #' . $appointment->id,
            ]);

            Consultation::create([
                'online_session_id' => $onlineSession->id,
                'patient'           => $patientUsername,
                'start_time'        => now(),
                'end_time'          => null,
                'notes'             => $request->notes,
                'paid_at'           => null,
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function fetchConsultations()
    {
        $user = auth()->user();

        if ($user->role === 'doctor') {
            $sessionIds = OnlineSession::where('doctor', $user->username)->pluck('id');

            $consultations = Consultation::with('onlineSession')
                ->whereIn('online_session_id', $sessionIds)
                ->orderBy('start_time', 'desc')
                ->get();
        } else {
            $consultations = Consultation::with('onlineSession')
                ->where('patient', $user->username)
                ->orderBy('start_time', 'desc')
                ->get();
        }

        $data = $consultations->map(function ($c) {
            return [
                'id'         => $c->id,
                'patient'    => $c->patient,
                'doctor'     => $c->onlineSession->doctor ?? '-',
                'start_time' => $c->start_time ? $c->start_time->format('d M Y H:i') : '-',
                'end_time'   => $c->end_time ? $c->end_time->format('d M Y H:i') : null,
                'notes'      => $c->notes,
                'is_active'  => is_null($c->end_time),
                'chat_url'   => '/chat/' . $c->id,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

}

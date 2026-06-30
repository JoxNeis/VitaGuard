<?php

// namespace App\Http\Controllers\Api;

// use App\Models\Appointment;
// use App\Models\Consultation;
// use App\Models\Doctor;
// use App\Models\DoctorSchedule;
// use App\Models\Specialty;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;

// class AppointmentController extends Controller
// {
//     public function index()
//     {
//         $doctors = Doctor::with(['specialties', 'schedules'])->get();
//         $specialties = Specialty::all();

//         return view('pages.consultations.index', compact('doctors', 'specialties'));
//     }

//     public function create()
//     {
//     }

//     public function store(Request $request)
//     {
//         if (!Auth::check()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Anda harus login terlebih dahulu untuk melakukan booking.'
//             ], 401);
//         }

//         $request->validate([
//             'doctor_id'   => 'required|exists:doctors,username',
//             'schedule_id' => 'required|exists:doctor_schedules,id',
//             'date'        => 'required|date|after_or_equal:today',
//             'notes'       => 'nullable|string|max:255',
//         ]);

//         $patientUsername = Auth::user()->username;

//         $existingAppointment = Appointment::where('patient', $patientUsername)
//             ->where('doctor_schedule_id', $request->schedule_id)
//             ->whereDate('date', $request->date)
//             ->where('status', '!=', 'cancelled')
//             ->first();

//         if ($existingAppointment) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Anda sudah melakukan booking untuk jadwal dan tanggal ini.'
//             ], 422);
//         }

//         DB::beginTransaction();
//         try {
//             $schedule = DoctorSchedule::findOrFail($request->schedule_id);

//             $queueOrder = Appointment::where('doctor_schedule_id', $schedule->id)
//                 ->whereDate('date', $request->date)
//                 ->count() + 1;

//             $appointment = Appointment::create([
//                 'patient'            => $patientUsername,
//                 'doctor_schedule_id' => $schedule->id,
//                 'date'               => $request->date,
//                 'time'               => $schedule->open_time,
//                 'queue_order'        => $queueOrder,
//                 'status'             => 'pending',
//                 'notes'              => $request->notes,
//             ]);

//             $onlineSession = \App\Models\OnlineSession::create([
//                 'doctor'           => $request->doctor_id,
//                 'start_time'       => now(),
//                 'end_time'         => null,
//                 'consultation_fee' => 0,
//                 'description'      => 'Appointment #' . $appointment->id,
//             ]);

//             Consultation::create([
//                 'online_session_id' => $onlineSession->id,
//                 'patient'           => $patientUsername,
//                 'start_time'        => now(),
//                 'end_time'          => null,
//                 'notes'             => $request->notes,
//                 'paid_at'           => null,
//             ]);

//             DB::commit();

//             return response()->json([
//                 'success'      => true,
//                 'message'      => 'Booking jadwal konsultasi berhasil.',
//                 'queue_order'  => $appointment->queue_order,
//                 'appointment'  => $appointment,
//             ]);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Gagal menyimpan data booking: ' . $e->getMessage()
//             ], 500);
//         }
//     }


//     public function show(string $id)
//     {
//     }

//     public function edit(string $id)
//     {
//     }

//     public function update(Request $request, string $id)
//     {
//     }

//     public function destroy(string $id)
//     {
//     }

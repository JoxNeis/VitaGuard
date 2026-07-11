<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Consultation;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{   
    /**
     * Display a listing of the resource.
     */
    public function index($consultation)
    {
        $consultation = Consultation::with([
            'patientData',
            'onlineSession.doctorData'
        ])->findOrFail($consultation);

        return response()->json([
            'success' => true,
            'data' => [
                'id'                => $consultation->id,
                'online_session_id' => $consultation->online_session_id,
                'patient' => optional($consultation->patientData)->first_name . ' ' .
                            optional($consultation->patientData)->last_name,
                'doctor'  => 'dr. ' .
                            optional($consultation->onlineSession->doctorData)->first_name . ' ' .
                            optional($consultation->onlineSession->doctorData)->last_name,
                'start_time'        => $consultation->start_time,
                'end_time'          => $consultation->end_time,
                'notes'             => $consultation->notes,
                'paid_at'           => $consultation->paid_at,
                'is_active'         => is_null($consultation->end_time),
            ]
        ]); 
    }

    public function fetchMessages($consultation)
    {
        $consultationData = Consultation::with([
            'patientData',
            'onlineSession.doctorData'
        ])->findOrFail($consultation);

        $chats = Chat::where('consultation_id', $consultation)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($chat) use ($consultationData) {

                if ($chat->sender == $consultationData->patient) {

                    $chat->sender_name =
                        optional($consultationData->patientData)->first_name . ' ' .
                        optional($consultationData->patientData)->last_name;

                } else {

                    $chat->sender_name =
                        'dr. ' .
                        optional($consultationData->onlineSession->doctorData)->first_name . ' ' .
                        optional($consultationData->onlineSession->doctorData)->last_name;
                }

                return $chat;
            });

        $isActive = is_null($consultationData->end_time);

        return response()->json([
            'success'      => true,
            'data'         => $chats,
            'is_active'    => $isActive,
            'current_user' => auth()->user()->username,
            'patient' =>
                optional($consultationData->patientData)->first_name . ' ' .
                optional($consultationData->patientData)->last_name,
            'doctor' =>
                'dr. ' .
                optional($consultationData->onlineSession->doctorData)->first_name . ' ' .
                optional($consultationData->onlineSession->doctorData)->last_name,
            'end_time'     => $consultationData->end_time,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'sender'          => 'required',
            'message'         => 'required|string',
        ]);

        $chat = Chat::create([
            'consultation_id' => $request->consultation_id,
            'sender'          => $request->sender,
            'message'         => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim',
            'data'    => $chat,
        ]);
    }

    public function close($consultation)
    {
        $consultation = Consultation::find($consultation);

        $consultation->update([
            'end_time' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konsultasi berhasil ditutup',
        ]);
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
}

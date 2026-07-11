<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineSession extends Model
{
    use HasFactory;

    protected $table = 'online_sessions'; 

    protected $fillable = [
        'doctor',
        'start_time',
        'end_time',
        'consultation_fee',
        'description',
    ];

    protected $casts = [
        'start_time'       => 'datetime',
        'end_time'         => 'datetime',
        'consultation_fee' => 'decimal:2',
    ];

    public function doctorData()
    {
        return $this->belongsTo(Doctor::class, 'doctor', 'username');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class,'online_session_id');
    }
}

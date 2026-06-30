<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function patient(){
        return $this->belongsTo(Member::class, 'patient', 'username');
    }

    public function schedule(){
        return $this->belongsTo(DoctorSchedule::class, 'doctor_schedule_id', 'id');
    }

    public function doctor(){
        return $this->schedule->belongsTo(Doctor::class,'doctor','username');
    }
}

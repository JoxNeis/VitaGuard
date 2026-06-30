<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    public function doctor(){
        return $this->belongsTo(Doctor::class,'doctor','username');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_schedule_id', 'id');
    }
}

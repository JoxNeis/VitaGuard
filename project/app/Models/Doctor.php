<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
     public function doctorSpecialty()
    {
        return $this->hasMany(DoctorSpecialty::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function onlineSession()
    {
        return $this->hasMany(OnlineSession::class);
    }

}

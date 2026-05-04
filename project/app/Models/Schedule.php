<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    public function doctor_username(){
        return $this->belongsTo(Doctor::class, 'doctor_username', 'username');
    }

    public function facility_id(){
        return $this->belongsTo(Facility::class, 'facility_id', 'id');
    }

    public function schedules(){
        return $this->hasMany(Schedule::class);
    }
}

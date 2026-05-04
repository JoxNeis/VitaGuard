<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    public function member_username(){
        return $this->belongsTo(Member::class, 'member_username', 'username');
    }

    public function schedule_id(){
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id');
    }

    public function appointments(){
        return $this->hasMany(Appointment::class);
    }
}

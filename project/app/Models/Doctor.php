<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = []; 

    public function specialties()
    {
        return $this->hasMany(DoctorSpecialty::class, 'doctor', 'username');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    public function district(){
        return $this->belongsTo(District::class, 'district_id');
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class,'doctor','username');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class,'doctor','username');
    }


}

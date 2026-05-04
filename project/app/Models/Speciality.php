<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use HasFactory;
    protected $table = 'specialties';
    public function doctorSpecialty()
    {
        return $this->hasMany(DoctorSpecialty::class);
    }
}

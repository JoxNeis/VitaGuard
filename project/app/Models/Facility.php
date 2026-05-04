<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function facilityHour()
    {
        return $this->hasMany(FacilityHour::class);
    }

    public function schedule()
    {
        return $this->hasMany(Schedule::class);
    }

    public function prescriptionDetails()
    {
        return $this->hasMany(PrescriptionDetail::class);
    }

    public function facilityAdmins()
    {
        return $this->hasMany(FacilityAdmin::class);
    }
}

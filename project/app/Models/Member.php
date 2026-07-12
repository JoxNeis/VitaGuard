<?php

namespace App\Models;

use App\Data\Medic\MedicalProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $primaryKey = 'username';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = []; 
    protected $fillable = [
        'username',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'date_of_birth',
        'address',
        'district_id',
    ];
    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function medicalProfile()
    {
        return $this->hasOne(MedicalProfile::class, 'member', 'username');
    }
    public function medicalHistories()
    {
        return $this->hasMany(MedicalHistory::class, 'member', 'username');
    }
    public function Prescriptions()
    {
        return $this->hasMany(Prescription::class, 'patient', 'username');
    }
}

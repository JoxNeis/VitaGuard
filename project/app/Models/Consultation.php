<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'online_session_id',
        'patient',
        'start_time',
        'end_time',
        'notes',
        'paid_at'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'paid_at'    => 'datetime',
    ];

    public function onlineSession()
    {
        return $this->belongsTo(OnlineSession::class, 'online_session_id');
    }

    public function patientData()
    {
        return $this->belongsTo(Member::class, 'patient', 'username');
    }

    public function isActive(): bool
    {
        return is_null($this->end_time);
    }
    
    public function chats()
    {
        return $this->hasMany(Chat::class, 'consultation_id')->orderBy('created_at', 'asc');
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }
    
}

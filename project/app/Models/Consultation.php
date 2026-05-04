<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    public function onlineSession()
    {
        return $this->belongsTo(OnlineSession::class, 'online_session_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_username', 'username');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
    
}

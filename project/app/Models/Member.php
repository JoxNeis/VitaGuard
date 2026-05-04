<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}

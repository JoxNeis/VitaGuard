<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    public function province()
    {
        $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        $this->hasMany(District::class);
    }
}

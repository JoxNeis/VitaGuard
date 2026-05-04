<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function City()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}

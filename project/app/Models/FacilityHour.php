<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityHour extends Model
{
    use HasFactory;
        public function facility()
        {
            return $this->belongsTo(Facility::class, 'facility_id');
        }
}

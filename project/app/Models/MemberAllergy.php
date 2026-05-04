<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberAllergy extends Model
{
    use HasFactory;

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_username', 'username');
    }

    public function allergen()
    {
        return $this->belongsTo(Allergen::class, 'allergen_id');
    }

    public function inputtedBy()
    {
        return $this->belongsTo(User::class, 'inputted_by', 'username');
    }
}

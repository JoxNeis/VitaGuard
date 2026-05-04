<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacilityHoursController extends Controller
{
    //
    public function index()
    {
        return view('pages.facilityHours.index');
    }
}

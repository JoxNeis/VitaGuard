<?php

namespace App\Http\Controllers\Facility_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    //
    public function index()
    {
        return view('pages.facility.index');
    }
}

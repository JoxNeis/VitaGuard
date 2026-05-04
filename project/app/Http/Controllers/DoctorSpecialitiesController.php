<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DoctorSpecialitiesController extends Controller
{
    //
    public function index()
    {
        return view('pages.doctorSpecialities.index');
    }
}

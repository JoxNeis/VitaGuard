<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DoctorSpecialitiesController extends Controller
{
    //
    public function index()
    {
        return view('pages.doctorSpecialities.index');
    }
}

<?php

namespace App\Http\Controllers\Facility_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.public.appointment.index');
    }
}

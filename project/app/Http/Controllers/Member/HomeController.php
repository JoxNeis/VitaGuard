<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleTopic;
use App\Models\Allergen;
use App\Models\City;
use App\Models\District;
use App\Models\MedicalHistory;
use App\Models\Member;
use App\Models\MemberAllergy;
use App\Models\Province;
use App\Models\User;
use App\Models\Speciality;
use App\Models\Doctor;
use App\Models\DoctorSpecialty;
use App\Models\Facility;
use App\Models\FacilityHour;
use App\Models\OnlineSession;
use App\Models\Consultation;
use App\Models\Chat;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\Medicine;
use App\Models\Schedule;
use App\Models\FacilityAdmin;
use App\Models\Appointment;


use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {        
        return view('pages.member.index');
    }
}

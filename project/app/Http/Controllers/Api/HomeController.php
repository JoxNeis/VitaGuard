<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

// for fetch list table instantly
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use Illuminate\Http\Request;
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
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\DoctorSpecialty;
use App\Models\Facility;
use App\Models\OnlineSession;
use App\Models\Consultation;
use App\Models\Chat;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\Medicine;
use App\Models\FacilityAdmin;
use App\Models\Appointment;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $articles = Article::latest()->take(5)->get();
        return view('pages.welcome', compact('articles'));
    }

    public function fetchAdminTable($tableName)
    {
        $hiddenTables = ['migrations', 'failed_jobs', 'password_reset_tokens', 'personal_access_tokens', 'sessions'];

        if (in_array($tableName, $hiddenTables) || !Schema::hasTable($tableName)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tabel tidak ditemukan atau akses ditolak.'
            ], 404);
        }
       
        $data = DB::table($tableName)->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getAvailableTables()
    {
        //get all table name di db
        $tablesList = array_map('current', DB::select('SHOW TABLES'));

        //blacklist table
        $hiddenTables = [
            'migrations',
            'failed_jobs',
            'password_reset_tokens',
            'personal_access_tokens',
            'sessions'
        ];

        $tables = [];
        foreach ($tablesList as $tableName) {
            if (!in_array($tableName, $hiddenTables)) {
                $tables[] = [
                    'id' => $tableName,
                    'name' => 'Data ' . ucfirst($tableName)
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $tables
        ]);
    }   
}

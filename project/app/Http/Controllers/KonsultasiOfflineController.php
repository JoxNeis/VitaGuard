<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KonsultasiOfflineController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.konsultasiOffline.index');
    }
}

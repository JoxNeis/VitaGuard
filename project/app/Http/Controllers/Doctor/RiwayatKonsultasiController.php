<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatKonsultasiController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.riwayatKonsultasi.index');
    }
}

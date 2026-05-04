<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // get from database
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);        

        // try to login here
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // get role from db
            $role = Auth::user()->role;

            switch ($role) {
                case 'admin':
                    return redirect()->intended('admin/home');
                case 'doctor':
                    return redirect()->intended('doctor/home');
                case 'member':
                    return redirect()->intended('member/home');
                case 'facility_admin':
                    return redirect()->intended('facility-admin/home');
            }
        }

        return back()->withErrors([
            'username' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('username');
    }
}

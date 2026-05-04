<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RiwayatKonsultasiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

// CONTROLLER FOR ADMIN
use App\Http\Controllers\Admin\{
    HomeController as AdminHomeController,
};

// CONTROLLER FOR MEMBER
use App\Http\Controllers\Member\{
    HomeController as MemberHomeController,
};

// CONTROLLER FOR FACILITY_ADMIN
use App\Http\Controllers\Facility_admin\{
    HomeController as FacilityHomeController,
};

// CONTROLLER FOR DOCTOR
use App\Http\Controllers\Doctor\{
    HomeController as DoctorHomeController,
    ConsultationController as DoctorConsultationController,
    AppointmentController as DoctorAppointmentController,
    // etc
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index']);
Route::resource('consultations', ConsultationController::class);
Route::resource('appointments', AppointmentController::class);
Route::resource('doctors', DoctorController::class);
Route::resource('history-consultations', RiwayatKonsultasiController::class);

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
Route::get('/register', [RegisterController::class, 'index'])->name('register');

// route that can be access after login
Route::middleware(['auth'])->group(function () {
    Route::prefix('member')->middleware(['auth', 'role:member'])->group(function () {
        // prefix otomatis membuat url menjadi /admin/{examplepages}
        Route::get('/home', [MemberHomeController::class, 'index'])->name('member.home');
    });

    Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/home', [AdminHomeController::class, 'index'])->name('admin.home');
    });

    Route::prefix('doctor')->middleware(['role:doctor'])->group(function () {

        Route::get('/home', [DoctorHomeController::class, 'index'])->name('doctor.home');
        Route::resource('/consultations', DoctorConsultationController::class);
        Route::resource('/appointments', DoctorAppointmentController::class);
    });

    Route::prefix('facility-admin')->middleware(['auth', 'role:facility_admin'])->group(function () {
        Route::get('/home', [FacilityHomeController::class, 'index'])->name('facility.home');
    });
});





// testing routes for error
// Route::get('/test-404', function () {
//     abort(404);
// });
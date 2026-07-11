<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Data\Value\Account\Role;

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


#region API
Route::prefix('api/')->group(function () {
    Route::get('articles/public', [ArticleController::class, 'getPublicArticles']);
    Route::get('articles/latest', [ArticleController::class, 'getLatestArticles']);
    Route::get('articles/topics', [ArticleController::class, 'getArticleTopics']);
    Route::get('articles/popular-topics', [ArticleController::class, 'getPopularArticleTopics']);

    Route::prefix('auth/')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::delete('logout', [AuthController::class, 'logout'])->name('logout');
        });
    });
    // Route::get(
    //     '/consultations/doctors',
    //     [ConsultationController::class, 'fetchDoctors']
    // );

    Route::middleware(['auth'])->group(function () {
        Route::get('articles/fetch', [ArticleController::class, 'fetchArticles']);
        Route::get('articles/create-data', [ArticleController::class, 'create']);
        Route::get('articles/{article}/detail', [ArticleController::class, 'show']);
        Route::get('articles/{article}/edit-data', [ArticleController::class, 'edit'])->middleware('can:update,article');
        Route::get('consultations/fetch', [ConsultationController::class, 'fetchConsultations']);
        Route::get('consultations/{username}/detail', [ConsultationController::class, 'show']);
        Route::get('/consultations/start/{doctor:username}', [ConsultationController::class, 'start'])
            ->name('consultation.start');
        Route::get('consultations/create-data', [ConsultationController::class, 'create']);
        Route::get('chat/{consultation}', [ChatController::class, 'fetchMessages']);
        Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
        Route::get('/appointments/fetch', [AppointmentController::class, 'fetchAppointments']);
        Route::get('/doctor/appointments/fetch', [AppointmentController::class, 'fetchDoctorAppointments']);
        Route::get('dashboard/fetch', [HomeController::class, 'fetchDashboardData']);
        // POST
        Route::post('articles/store', [ArticleController::class, 'store'])->middleware('can:create,article');
        Route::post('articles/{article}/update', [ArticleController::class, 'update'])->middleware('can:update,article');
        Route::post('articles/{article}/destroy', [ArticleController::class, 'destroy'])->middleware('can:delete,article');
        Route::post('chat/send', [ChatController::class, 'store']);
        Route::post('chat/{consultation}/close', [ChatController::class, 'close']);
        Route::get('profile/fetch', [ProfileController::class, 'fetch']);
        Route::post('profile/update', [ProfileController::class, 'update']);
    });

    Route::middleware(['auth', 'can:' . Role::ADMIN->value])->prefix('admin')->group(function () {
        Route::get('available-tables', [HomeController::class, 'getAvailableTables']);
        Route::get('fetch-table/{tableName}', [HomeController::class, 'fetchAdminTable']);
        Route::get('doctors/fetch', [DoctorController::class, 'fetchDoctors']);
        Route::get('doctors/create-data', [DoctorController::class, 'create']);
        Route::get('doctors/{username}/edit-data', [DoctorController::class, 'edit']);
        Route::get('members/fetch', [MemberController::class, 'fetchmembers']);
        Route::get('members/{username}/detail', [MemberController::class, 'show']);
        Route::get('members/create-data', [MemberController::class, 'create']);
        Route::get('members/{username}/edit-data', [MemberController::class, 'edit']);
        Route::get('users/fetch', [UserController::class, 'fetchusers']);
        Route::get('users/{username}/detail', [UserController::class, 'show']);
        Route::get('users/create-data', [UserController::class, 'create']);
        Route::get('users/{username}/edit-data', [UserController::class, 'edit']);
        Route::get('consultations/fetch-all', [ConsultationController::class, 'fetchAllConsultations']);
        Route::get('consultations/{username}/edit-data', [ConsultationController::class, 'edit']);

        // POST
        Route::post('doctors/store', [DoctorController::class, 'store']);
        Route::post('doctors/{username}/update', [DoctorController::class, 'update']);
        Route::post('members/store', [MemberController::class, 'store']);
        Route::post('members/{username}/update', [MemberController::class, 'update']);
        Route::post('users/store', [UserController::class, 'store']);
        Route::post('users/{username}/update', [UserController::class, 'update']);
        Route::post('consultations/store', [ConsultationController::class, 'store']);
        Route::post('consultations/{username}/update', [ConsultationController::class, 'update']);
        // DELETE        
        Route::post('doctors/{doctor}/destroy', [DoctorController::class, 'destroy'])->name('doctor.deleteData');
        Route::post('members/{username}/destroy', [MemberController::class, 'destroy'])->name('member.deleteData');
        Route::post('users/{username}/destroy', [UserController::class, 'destroy'])->name('user.deleteData');
        Route::post('consultations/{id}/destroy', [ConsultationController::class, 'destroy'])->name('consultation.deleteData');
    });
});
#endregion

#region PAGE
Route::redirect('/home', '/');
Route::view('/', 'pages.welcome');
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
Route::get('/consultations/{specialty?}', [ConsultationController::class, 'index'])->name('consultation.index');
Route::view('/articles', 'pages.articles.index');


Route::middleware(['auth'])->group(function () {    
    Route::get('/articles/{article}', function ($article) {
        return view('pages.articles.show', ['articleId' => $article]);
    });
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultation.store');

    Route::get('/chat/{consultation}', function ($consultation) {
        return view('pages.chat.index', ['consultationId' => $consultation]);
    })->name('chat');
    Route::view('/profile', 'pages.profiles.index')->name('profile');

    //PORTAL (ADMIN & DOCTOR)
    Route::prefix('portal')->middleware(['can:viewBackend,App\Models\Article'])->group(function () {

        Route::view('/', 'pages.admin.dashboard.index')->name('portal.dashboard');

        Route::prefix('articles')->group(function () {
            Route::view('/', 'pages.admin.articles.index');
            Route::view('/create', 'pages.admin.articles.create')->name('articles.create');
            Route::view('/{article}/show', 'pages.admin.articles.show');
            Route::view('/{article}/edit', 'pages.admin.articles.edit');
        });

        Route::prefix('consultations')->group(function () {
            Route::view('/', 'pages.admin.consultations.index');
            Route::view('/create', 'pages.admin.consultations.create');
            Route::view('/{username}/show', 'pages.admin.consultations.show');
            Route::view('/{username}/edit', 'pages.admin.consultations.edit');
        });
    });


    //ADMIN ONLY
    Route::prefix('admin')->middleware(['can:' . Role::ADMIN->value])->group(function () {

        Route::redirect('/', '/portal');
        Route::redirect('/home', '/portal');

        Route::prefix('doctors')->group(function () {
            Route::view('/', 'pages.admin.doctors.index');
            Route::view('/create', 'pages.admin.doctors.create')->name('doctor.create');
            Route::view('/{username}/edit', 'pages.admin.doctors.edit');
        });

        Route::prefix('users')->group(function () {
            Route::view('/', 'pages.admin.users.index');
            Route::view('/create', 'pages.admin.users.create');
            Route::view('/{username}/show', 'pages.admin.users.show');
            Route::view('/{username}/edit', 'pages.admin.users.edit');
        });

        Route::prefix('members')->group(function () {
            Route::view('/', 'pages.admin.members.index');
            Route::view('/create', 'pages.admin.members.create');
            Route::view('/{username}/show', 'pages.admin.members.show');
            Route::view('/{username}/edit', 'pages.admin.members.edit');
        });
    });


    //MEMBER ONLY
    Route::middleware(['can:' . Role::MEMBER->value])->group(function () {
        Route::view('/member/consultations', 'pages.consultations.member')->name('consultations.member');
        Route::get('/appointments/member', [AppointmentController::class, 'member'])->name('appointments.member');
    });

    // DOCTOR ONLY
    Route::middleware(['can:' . Role::DOCTOR->value])->group(function () {
        Route::get('/appointments/doctor', [AppointmentController::class, 'doctor'])->name('appointments.doctor');

        Route::prefix('doctor')->group(function () {
            Route::redirect('/', '/portal');
            Route::view('/consultations', 'pages.consultations.doctor');
        });
    });
});
#endregion

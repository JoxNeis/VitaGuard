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
    Route::get('articles/latest', [ArticleController::class, 'getLatestArticles']);
    Route::get('articles/topics', [ArticleController::class, 'getArticleTopics']);
    Route::get('articles/popular-topics', [ArticleController::class, 'getPopularArticleTopics']);

    Route::prefix('auth/')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::delete('logout', [AuthController::class, 'logout'])->name('logout');
        });
    });
    Route::get('/consultations/doctors', [ConsultationController::class, 'fetchDoctors']
);

    Route::middleware(['auth'])->group(function () {
        Route::get('articles/fetch', [ArticleController::class, 'fetchArticles']);
        Route::get('articles/create-data', [ArticleController::class, 'create']);
        Route::get('articles/{article}/detail', [ArticleController::class, 'show']);
        Route::get('articles/{article}/edit-data', [ArticleController::class, 'edit'])->middleware('can:update,article');
        Route::get('consultations/fetch', [ConsultationController::class, 'fetchConsultations']);
        Route::post('/consultations', [ConsultationController::class, 'store'])
        ->name('consultation.store');
        Route::get('chat/{consultation}', [ChatController::class, 'fetchMessages']);   
        Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']); 
        Route::middleware(['auth'])->group(function () {
});
        // POST
        Route::post('articles/store', [ArticleController::class, 'store'])->middleware('can:create,article');
        Route::post('articles/{article}/update', [ArticleController::class, 'update'])->middleware('can:update,article');
        Route::post('articles/{article}/destroy', [ArticleController::class, 'destroy'])->middleware('can:delete,article');
        Route::post('chat/send', [ChatController::class, 'store']);
        Route::post('chat/{consultation}/close', [ChatController::class, 'close']);

    });

    Route::middleware(['auth', 'can:' . Role::ADMIN->value])->prefix('admin')->group(function () {
        Route::get('dashboard', [HomeController::class, 'adminDashboard']);
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
        Route::get('consultations/{username}/detail', [ConsultationController::class, 'show']);
        Route::get('consultations/create-data', [ConsultationController::class, 'create']);
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
        Route::post('consultations/{username}/destroy', [ConsultationController::class, 'destroy'])->name('consultation.deleteData');
    });
});
#endregion

#region PAGE
Route::get('/', function () {
    return view('pages.welcome');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');


Route::get('/appointments', [AppointmentController::class, 'index'])
    ->name('appointments.index');
    

Route::post('/appointments', [AppointmentController::class, 'store'])
    ->name('appointments.store')
    ->middleware('auth');

Route::get('/api/appointments/fetch', [AppointmentController::class, 'fetchAppointments'])
    ->middleware('auth');

Route::get('/api/doctor/appointments/fetch', 
            [AppointmentController::class, 'fetchDoctorAppointments'])->middleware('auth');;

Route::prefix('consultations')->group(function () {

    // UI Booking
    Route::get('/{specialty?}', [ConsultationController::class, 'index'])
        ->name('consultation.index');
    
    // Proses booking 
    Route::post('/', [ConsultationController::class, 'store'])
        ->name('consultation.store')
        ->middleware('auth');
});


Route::middleware(['auth'])->group(function () {
    //can be access from admin & doctor
    Route::prefix('portal/articles')->group(function () {
        Route::get('/', function () {
            return view('pages.admin.articles.index');
        });

        Route::get('/create', function () {
            return view('pages.admin.articles.create');
        })->name('articles.create');

        Route::get('/{article}/show', function () {
            return view('pages.admin.articles.show');
        });

        Route::get('/{article}/edit', function () {
            return view('pages.admin.articles.edit');
        });

    });

    Route::get('/home', function () {
        return view('pages.home.index');
    });

    Route::get('/consultations', function () {
        return view('pages.consultations.member');
    });
    

    Route::get('/chat/{consultation}', [ChatController::class, 'show'])->name('chat.show');

    Route::get('/chat/{consultation}', function ($consultation) {
    return view('pages.chat.index', [
        'consultationId' => $consultation
    ]);
})->name('chat');

    Route::prefix('admin')->middleware('can:' . Role::ADMIN->value)->group(function () {
        Route::get('/home', function () {
            return redirect('admin');
        });
        Route::get('/', function () {
            return view('pages.admin.index');
        });
        Route::get('/doctors', function () {
            return view('pages.admin.doctors.index');
        });

        Route::get('doctors/create', function () {
            return view('pages.admin.doctors.create');
        })->name('doctor.create');

        Route::get('doctors/{username}/edit', function () {
            return view('pages.admin.doctors.edit');
        });

        Route::get('/consultation', function () {
            return view('pages.admin.consultation');
        });
        
        Route::get('members', function(){
            return view('pages.admin.members.index');
        });

        Route::get('members/create', function () {
            return view('pages.admin.members.create');
        });

        Route::get('members/{username}/show', function () {
            return view('pages.admin.members.show');
        });

        Route::get('members/{username}/edit', function () {
            return view('pages.admin.members.edit');
        });        
        Route::get('users', function(){
            return view('pages.admin.users.index');
        });

        Route::get('users/create', function () {
            return view('pages.admin.users.create');
        });

        Route::get('users/{username}/show', function () {
            return view('pages.admin.users.show');
        });

        Route::get('users/{username}/edit', function () {
            return view('pages.admin.users.edit');
        });        
        Route::get('consultations', function(){
            return view('pages.admin.consultations.index');
        });

        Route::get('consultations/create', function () {
            return view('pages.admin.consultations.create');
        });

        Route::get('consultations/{username}/show', function () {
            return view('pages.admin.consultations.show');
        });

        Route::get('consultations/{username}/edit', function () {
            return view('pages.admin.consultations.edit');
        });        
    });

    Route::prefix('doctor')->middleware('can:' . Role::DOCTOR->value)->group(function () {
        Route::get('/', function () {
            return view('pages.doctors.index');
        })->name('doctor.index');

        Route::get('/consultations', function () {
            return view('pages.consultations.doctor');
        });

    });
});

//appointments ROLE AND MEMBER
Route::middleware(['auth', 'can:' . Role::DOCTOR->value])->group(function () {

    Route::get('/appointments/doctor', [AppointmentController::class, 'doctor'])
        ->name('appointments.doctor');

});

Route::middleware(['auth', 'can:' . Role::MEMBER->value])->group(function () {

    Route::get('/member/consultations', function () {
        return view('pages.consultations.member');
    })->name('consultations.member');

    Route::get('/appointments/member', [AppointmentController::class, 'member'])
    ->name('appointments.member')
    ->middleware('auth');
});


#endregion

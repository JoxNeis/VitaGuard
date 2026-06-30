<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ConsultationController;
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
            Route::delete('logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('articles/fetch', [ArticleController::class, 'fetchArticles']);
        Route::get('articles/create-data', [ArticleController::class, 'create']);
        Route::get('articles/{article}/detail', [ArticleController::class, 'show']);
        Route::get('articles/{article}/edit-data', [ArticleController::class, 'edit'])->middleware('can:update,article');
        Route::get('consultations/fetch', [ConsultationController::class, 'fetchConsultations']);
        Route::get('chat/{consultation}', [ChatController::class, 'fetchMessages']);
        
        // POST
        Route::post('articles/store', [ArticleController::class, 'store']);
        Route::post('articles/{article}/update', [ArticleController::class, 'update'])->middleware('can:update,article');
        Route::post('articles/{article}/destroy', [ArticleController::class, 'destroy'])->middleware('can:delete,article');
        Route::post('chat/send', [ChatController::class, 'store']);
        Route::post('chat/{consultation}/close', [ChatController::class, 'close']);
    });

    Route::middleware(['auth', 'can:' . Role::ADMIN->value])->prefix('admin')->group(function () {
        Route::get('available-tables', [HomeController::class, 'getAvailableTables']);
        Route::get('fetch-table/{tableName}', [HomeController::class, 'fetchAdminTable']);
        Route::get('doctors/fetch', [DoctorController::class, 'fetchDoctors']);
        Route::get('doctors/create-data', [DoctorController::class, 'create']);
        Route::get('doctors/{username}/edit-data', [DoctorController::class, 'edit']);

        // POST
        Route::post('doctors/store', [DoctorController::class, 'store']);
        Route::post('doctors/{username}/update', [DoctorController::class, 'update']);
        // DELETE        
        Route::post('doctors/{doctor}/destroy', [DoctorController::class, 'destroy'])->name('doctor.deleteData');
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

Route::get('/doctors', function () {
    return view('pages.doctors.index');
});

Route::middleware(['auth'])->group(function () {
    //can be access from admin & doctor
    Route::prefix('admin/articles')->group(function () {
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

    Route::get('/appointments', function () {
        return view('pages.appointments.index');
    });

    Route::get('/doctors', function () {
        return view('pages.doctors.index');
    });

    Route::get('/chat/{consultation}', function ($consultation) {
        return view('pages.chat.index', ['consultationId' => $consultation]);
    });

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
    });

    Route::prefix('doctor')->middleware('can:' . Role::DOCTOR->value)->group(function () {
        Route::get('/', function () {
            return view('pages.doctors.index');
        });

        Route::get('/consultations', function () {
            return view('pages.consultations.doctor');
        });
    });
});
#endregion

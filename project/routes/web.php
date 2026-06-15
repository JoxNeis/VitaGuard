<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\HomeController;
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

    Route::prefix('auth/')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->group(function () {
            Route::delete('logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware(['auth', 'can:' . Role::ADMIN->value])->prefix('admin')->group(function () {
        Route::get('available-tables', [HomeController::class, 'getAvailableTables']); 
        Route::get('fetch-table/{tableName}', [HomeController::class, 'fetchAdminTable']);        
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

Route::middleware(['auth'])->group(function () {

    Route::prefix('admin')->middleware('can:' . Role::ADMIN->value)->group(function () {
        Route::get('/', function () {
            return view('pages.admin.index');
        });
    });

    Route::prefix('doctor')->middleware('can:' . Role::DOCTOR->value)->group(function () {
        Route::get('/');
    });
});
#endregion

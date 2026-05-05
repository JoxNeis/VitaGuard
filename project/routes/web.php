<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

#region MAIN APP ROUTE
#region AUTH
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
#endregion
#endregion

#region MAIN VIEW ROUTE
Route::get('/', [HomeController::class, 'index'])->name('landing');
Route::view('/login', 'auth.login')->name('login');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles');
Route::get('/articles/{id}',[ArticleController::class, 'show'])->name('read-articles');
Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors');
#endregion
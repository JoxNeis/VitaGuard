<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    #region API ROUTES (TEMPORARY)
    #endregion

    #region VIEW ROUTES
    Route::get("/home", [HomeController::class, 'index'])->name('home');
    #endregion
});
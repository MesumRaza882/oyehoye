<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::middleware(['auth:admin', 'preventBackHistory'])->group(function () {
    //Admin Profile
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/home', 'index')->name('admin.home');
    });

    Route::group(['prefix' => 'admin'], function () {
 
        Route::middleware(['editor'])->group(function () {
            
        });
    });
});

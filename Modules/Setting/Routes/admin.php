<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => ['superadmin']], function () {
    Route::middleware(['auth:admin'])->group(function(){
        Route::get('setting','SettingController@index')->name('settings');
        Route::post('setting/save','SettingController@save')->name('settings.save');
    });
});
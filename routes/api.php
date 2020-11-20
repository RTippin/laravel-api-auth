<?php

use App\Http\Controllers\Auth\ApiForgotPassword;
use App\Http\Controllers\Auth\ApiLogin;
use App\Http\Controllers\Auth\ApiLogout;
use App\Http\Controllers\Auth\ApiRefresh;
use App\Http\Controllers\Auth\ApiRegister;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group( function(){
    Route::post('login', ApiLogin::class)->name('login');
    Route::post('register', ApiRegister::class)->name('register');
    Route::post('refresh', ApiRefresh::class)->name('refresh');
    Route::post('logout', ApiLogout::class)->name('logout');
    Route::post('password/email', ApiForgotPassword::class)->name('password.email');
});

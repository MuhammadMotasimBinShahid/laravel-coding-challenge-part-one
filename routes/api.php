<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
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

////////////////////////////////////////////////////
// Public Section
////////////////////////////////////////////////////

Route::middleware('guest')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

////////////////////////////////////////////////////
// End Public Section
////////////////////////////////////////////////////



////////////////////////////////////////////////////
// Protected Section
////////////////////////////////////////////////////

Route::middleware('auth:sanctum')->group(function () {

    ////////////////////////////////////////////////////
    // User Section
    ////////////////////////////////////////////////////

    Route::prefix('user')->group(function () {
        Route::post('refresh-token', [AuthController::class, 'refreshToken'])->name('refreshToken');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });

    ////////////////////////////////////////////////////
    // User Section End
    ////////////////////////////////////////////////////


});

////////////////////////////////////////////////////
// End Protected Section
////////////////////////////////////////////////////

<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

////////////////////////////////////////////////////
// Protected Section
////////////////////////////////////////////////////

Route::middleware('auth:sanctum')->group(function () {

    ////////////////////////////////////////////////////
    // Users Connection Section
    ////////////////////////////////////////////////////

    Route::prefix('users')->group(function () {
        Route::get('suggestions', [UserController::class, 'index'])->name('getSuggestions');

        Route::prefix('requests')->group(function () {
            Route::post('', [UserController::class, 'store'])->name('sendRequest');

            Route::get('sent', [UserController::class, 'show'])->name('getRequests');

            Route::get('received', [UserController::class, 'receivedRequests'])->name('getReceivedRequests');

            Route::delete('{requestId}', [UserController::class, 'destroy'])->name('withdrawRequest');

            Route::put('', [UserController::class, 'update'])->name('acceptRequest');
        });

        Route::prefix('connections')->group(function () {
            Route::get('', [UserController::class, 'getConnections'])->name('getConnections');

            Route::delete('{connectionId}', [UserController::class, 'destroyConnection'])->name('removeConnection');
        });

        Route::delete('connections/{connectionId}', [UserController::class, 'destroyConnection'])->name('removeConnection');

        Route::get('{userId}/connections-in-common', [UserController::class, 'connectionsInCommon'])->name('getCommonConnections');
    });

    ////////////////////////////////////////////////////
    // Users Connection Section End
    ////////////////////////////////////////////////////


});

////////////////////////////////////////////////////
// End Protected Section
////////////////////////////////////////////////////

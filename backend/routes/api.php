<?php

use App\Http\Controllers\API\V1\AuthApiController;
use App\Http\Controllers\API\V1\CommentController;
use App\Http\Controllers\API\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('auth/login', [AuthApiController::class, 'login']); //

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::controller(TicketController::class)->group(function () {
            Route::get('tickets', 'index');               //
            Route::post('tickets', 'store');              // 
            Route::get('tickets/{id}', 'show');           // 
            Route::patch('tickets/{id}/status', 'updateStatus'); // 
        });

        Route::post('tickets/{id}/comments', [CommentController::class, 'store']); //

        Route::post('auth/logout', [AuthApiController::class, 'logout']); // 
    });
});

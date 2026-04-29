<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\V1\PetActionController;
use App\Http\Controllers\Api\V1\PetChatController;
use App\Http\Middleware\SimpleApiAuth;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(SimpleApiAuth::class)->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('v1')->group(function () {
        Route::apiResource('pets', PetController::class);
        Route::post('pets/{pet}/actions', [PetActionController::class, 'execute']);
        Route::get('pets/{pet}/actions', [PetActionController::class, 'history']);
        Route::post('pets/{pet}/chat', [PetChatController::class, 'chat']);
        Route::get('pets/{pet}/memories', [PetChatController::class, 'memories']);
    });
});
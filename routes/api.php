<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\V1\PetActionController;
use App\Http\Controllers\Api\V1\PetChatController;
use App\Http\Controllers\Api\V1\PetQuotaController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('v1')->group(function () {
        Route::apiResource('pets', PetController::class);
        Route::post('pets/{pet}/actions', [PetActionController::class, 'execute']);
        Route::get('pets/{pet}/actions', [PetActionController::class, 'history']);
        Route::get('pets/{pet}/quota', [PetQuotaController::class, 'show']);
        Route::post('pets/{pet}/chat', [PetChatController::class, 'chat'])
            ->middleware('streaming.quota');
        Route::post('pets/{pet}/chat/stream', [PetChatController::class, 'stream'])
            ->middleware('streaming.quota');
        Route::get('pets/{pet}/memories', [PetChatController::class, 'memories']);
    });
});

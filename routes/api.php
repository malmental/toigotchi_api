<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\V1\PetActionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::apiResource('pets', PetController::class);
        Route::post('pets/{pet}/actions', [PetActionController::class, 'execute']);
        Route::get('pets/{pet}/actions', [PetActionController::class, 'history']);
    });
});
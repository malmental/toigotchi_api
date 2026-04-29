<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PetController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('pets', PetController::class);
});

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\LocationController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login',    [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/locations', [LocationController::class, 'store']);
    Route::post('/locations/bulk',  [LocationController::class, 'bulk']);
    Route::get('/me/last-location', [LocationController::class, 'myLast']);
    Route::get('/users/{id}/last-location', [LocationController::class, 'userLast']);
    Route::get('/users/{id}/locations',    [LocationController::class, 'userLocations']);
    Route::get('/locations/last', [LocationController::class, 'lastAll']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



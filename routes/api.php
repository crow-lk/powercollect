<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConsumerController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\ConsumerUsageController;

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // Consumer routes
    Route::apiResource('consumers', ConsumerController::class);
    
    // Equipment routes
    Route::apiResource('equipment', EquipmentController::class);
    
    // Consumer Usage routes
    Route::apiResource('consumer-usage', ConsumerUsageController::class);
    Route::get('/statistics', [ConsumerUsageController::class, 'statistics']);
});

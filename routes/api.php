<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\CustomerUsageController;

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // Customer routes
    Route::apiResource('customers', CustomerController::class);
    
    // Equipment routes
    Route::apiResource('equipment', EquipmentController::class);
    
    // Customer Usage routes
    Route::apiResource('customer-usage', CustomerUsageController::class);
    Route::get('/statistics', [CustomerUsageController::class, 'statistics']);
});

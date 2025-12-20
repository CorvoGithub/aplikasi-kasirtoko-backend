<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;

// ========= Public Routes =========
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// ========= Protected Routes =========
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    //-- Dashboard Routes
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);
    Route::get('/notifications', [App\Http\Controllers\DashboardController::class, 'notifications']);

    //-- Product Routes
    Route::resource('products', \App\Http\Controllers\ProductController::class);

    //-- Transaction Routes
    Route::post('/transactions', [\App\Http\Controllers\TransactionController::class, 'store']);
    Route::get('/transactions', [\App\Http\Controllers\TransactionController::class, 'index']);

    //-- Profile Routes
    Route::put('/profile/update', [ProfileController::class, 'updateProfile']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
});
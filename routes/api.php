<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TinderController;
use App\Http\Controllers\API\AuthController; // Assuming you have a standard auth controller

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Requires Access Token)
Route::middleware('auth:api')->group(function () {
    
    // 1. Get Recommendations (Pagination included)
    Route::get('/profiles', [TinderController::class, 'getRecommendations']);
    
    // 2. Swipe Action (Like/Dislike)
    Route::post('/swipe', [TinderController::class, 'swipe']);
    
    // 3. Get Liked List
    Route::get('/likes', [TinderController::class, 'getLikedProfiles']);
    
    // User Info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
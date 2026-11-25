<?php

use App\Http\Controllers\Api\UserInsightController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Endpoint API Analitik & Rekomendasi GameBacklog
Route::get('/users/{userId}/insights', [UserInsightController::class, 'userInsights']);
Route::get('/users/{userId}/recommendations', [UserInsightController::class, 'recommendations']);
Route::get('/community/trending', [UserInsightController::class, 'communityTrending']);
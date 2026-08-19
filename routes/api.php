<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ModelController;
use Illuminate\Support\Facades\Route;

Route::post('/chat', [ChatController::class, 'handle']);
Route::get('/models', [ModelController::class, 'index']);
Route::get('/health', [HealthController::class, 'index']);
Route::post('/feedback', [FeedbackController::class, 'store']);

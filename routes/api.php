<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserMediaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes (Bisa langsung di-test di Postman seperti di foto)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/media', [MediaController::class, 'index']);
Route::post('/media', [MediaController::class, 'store']);
Route::get('/media/{id}', [MediaController::class, 'show']);
Route::put('/media/{id}', [MediaController::class, 'update']);
Route::delete('/media/{id}', [MediaController::class, 'destroy']);

// Protected routes (Relasi Many-to-Many User <-> Media)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/my-media', [UserMediaController::class, 'index']);
    Route::post('/my-media', [UserMediaController::class, 'attach']);
    Route::put('/my-media/{mediaId}', [UserMediaController::class, 'updateStatus']);
    Route::delete('/my-media/{mediaId}', [UserMediaController::class, 'detach']);
});
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Requires Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Blog Post Protected Routes
    Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index']);
    Route::get('/posts/{post}', [\App\Http\Controllers\PostController::class, 'show']);
    Route::get('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'index']);
    Route::post('/posts', [\App\Http\Controllers\PostController::class, 'store']);
    Route::put('/posts/{post}', [\App\Http\Controllers\PostController::class, 'update']);
    Route::delete('/posts/{post}', [\App\Http\Controllers\PostController::class, 'destroy']);
    
    // Comments Protected Routes
    Route::post('/posts/{post}/comments', [\App\Http\Controllers\CommentController::class, 'store']);
    Route::put('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [\App\Http\Controllers\CommentController::class, 'destroy']);
});



<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Middleware\AuthMiddleware;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::post("/auth/login", [AuthController::class, 'login']);
Route::post("/auth/register", [AuthController::class, 'register']);

Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);

Route::get('/', function () {
    return ApiResponse::success('success');
});

Route::middleware(AuthMiddleware::class)->group(function () {
   
});

Route::get('/auth/profile', [AuthController::class, 'profile']);
Route::post("/auth/logout", [AuthController::class, 'logout']);

Route::post('/books', [BookController::class, 'store']);
Route::put('/books/{id}', [BookController::class, 'update']);
Route::delete('/books/{id}', [BookController::class, 'destroy']);
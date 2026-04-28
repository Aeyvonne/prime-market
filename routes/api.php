<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Produits
    Route::get('/products',         [ProductController::class, 'index']);
    Route::get('/products/{id}',    [ProductController::class, 'show']);
    Route::post('/products',        [ProductController::class, 'store']);
    Route::put('/products/{id}',    [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Catégories
    Route::get('/categories',      [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::post('/categories',     [CategoryController::class, 'store']);

    // Commandes
    Route::get('/orders',        [OrderController::class, 'index']);
    Route::get('/orders/{id}',   [OrderController::class, 'show']);
    Route::post('/orders',       [OrderController::class, 'store']);
    Route::put('/orders/{id}',   [OrderController::class, 'updateStatus']);
});
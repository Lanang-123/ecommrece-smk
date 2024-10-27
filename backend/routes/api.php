<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PromoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
Route::get('/me', [AuthController::class, 'me'])->middleware(['auth:sanctum']);
Route::post('/register', [AuthController::class, 'register']);


// Product
Route::prefix('/products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/category/{id_category}', [ProductController::class, 'showByCategory']);
    Route::get('/images/{filename}', [ProductController::class, 'getImage']);
    Route::get('/name/{productName}', [ProductController::class, 'showByName']);
    Route::post('/{id_category}', [ProductController::class, 'store'])->middleware(['auth:sanctum']);
    Route::post('/update/{id}', [ProductController::class, 'update'])->middleware(['auth:sanctum']);
    Route::get('/delete/{id}', [ProductController::class, 'destroy'])->middleware(['auth:sanctum']);
});


// Category
Route::prefix('/category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store'])->middleware(['auth:sanctum']);
    Route::post('/{id}', [CategoryController::class, 'update'])->middleware(['auth:sanctum']);
    Route::get('/delete/{id}', [CategoryController::class, 'destroy'])->middleware(['auth:sanctum']);
});


// Cart
Route::prefix('/carts')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::get('/me-cart', [CartController::class, 'showByUser'])->middleware(['auth:sanctum']);

    Route::post('/', [CartController::class, 'store'])->middleware(['auth:sanctum']);
    Route::post('/{id}', [CartController::class, 'update'])->middleware(['auth:sanctum']);
    Route::get('/delete/{id}', [CartController::class, 'destroy'])->middleware(['auth:sanctum']);
});


// Orders
Route::post('/order', [OrderController::class, 'store'])->middleware(['auth:sanctum']);
Route::get('/order', [OrderController::class, 'all'])->middleware(['auth:sanctum']);
Route::get('/my-order', [OrderController::class, 'show'])->middleware(['auth:sanctum']);


// Promo
Route::prefix('/promo')->group(function () {
    Route::get('/', [PromoController::class, 'index']);
    Route::post('/', [PromoController::class, 'store'])->middleware(['auth:sanctum']);
    Route::post('/{id}', [PromoController::class, 'update'])->middleware(['auth:sanctum']);
    Route::get('/delete/{id}', [PromoController::class, 'destroy'])->middleware(['auth:sanctum']);
});
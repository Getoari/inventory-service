<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('products', ProductController::class)
    ->names('api.products');

Route::apiResource('orders', OrderController::class)
    ->only([
        'index',
        'store',
        'show',
    ])->names('api.orders');
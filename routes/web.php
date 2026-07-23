<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::get('/products', function () {
    return inertia('Products/Index');
})->name('products.index');

Route::get('/orders/create', function () {
    return inertia('Orders/Create');
})->name('orders.create');

Route::get('/orders/{order}', function ($order) {
    return inertia('Orders/Show', [
        'orderId' => $order,
    ]);
})->name('orders.show');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

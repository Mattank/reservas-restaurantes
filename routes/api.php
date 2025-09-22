<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RestaurantController;

Route::middleware('api.key')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
        Route::post('/restaurants', [RestaurantController::class, 'store'])->name('restaurants.store');
        Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');
        Route::put('/restaurants/{id}', [RestaurantController::class, 'update'])->name('restaurants.update');
        Route::delete('/restaurants/{id}', [RestaurantController::class, 'destroy'])->name('restaurants.destroy');
    });
});
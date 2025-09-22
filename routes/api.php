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

        Route::get('/reservas', [ReservasController::class, 'index'])->name('reservas.index');
        Route::post('/reservas', [ReservasController::class, 'store'])->name('reservas.store');
        Route::get('/reservas/{id}', [ReservasController::class, 'show'])->name('reservas.show');
        Route::put('/reservas/{id}', [ReservasController::class, 'update'])->name('reservas.update');
        Route::delete('/reservas/{id}', [ReservasController::class, 'destroy'])->name('reservas.destroy');

         Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
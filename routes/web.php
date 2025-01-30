<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'totalVehicles' => \App\Models\Vehicle::count(),
            'totalUsers' => \App\Models\User::count(),
            'recentVehicles' => \App\Models\Vehicle::latest()->take(5)->get(),
            'isAdmin' => Auth::user()->roles->contains('name', 'admin'),
        ]);
    })->name('dashboard');

    // User management routes
    Route::resource('users', UserController::class);

    // Vehicle management routes
    Route::resource('vehicles', VehicleController::class);

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::post('/users/{user}/reset-data', [UserController::class, 'resetUserData'])
            ->name('users.reset-data');
        Route::put('/users/{user}/toggle-status', [UserController::class, 'toggleUserStatus'])
            ->name('users.toggle-status');
    });
});

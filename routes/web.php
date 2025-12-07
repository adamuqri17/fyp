<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;

// --- Public Routes (Closures for now) ---
Route::get('/', function () {
    return view('homepage');
});

Route::get('map', function () {
    return view('map');
});

Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');

// --- Admin Guest Routes (Login) ---
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});

// --- Protected Admin Routes ---
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Dashboard (Closure for testing UI)
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // CRUD UI Routes (Closures for testing UI)
    Route::get('/graves/create', function () { 
        return view('admin.graves.create'); 
    });
    
    Route::get('/deceased/create', function () { 
        return view('admin.deceased.create'); 
    });
});
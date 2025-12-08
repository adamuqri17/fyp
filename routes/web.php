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

Route::get('/search', function () {
    return view('public.results');
})->name('grave.search');

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
    
    // --- NEW: REAL CRUD ROUTES ---
    // This one line creates all routes (index, create, store, edit, update, destroy)
    Route::resource('/graves', \App\Http\Controllers\AdminGraveController::class, [
        'as' => 'admin' // Prefixes routes names with 'admin.' (e.g., admin.graves.index)
    ]);
    
    // Keep this for now until we build the Deceased Controller
    Route::get('/deceased/create', function () { 
        return view('admin.deceased.create'); 
    });

    // Visual Map Manager
    Route::get('/map-manager', [App\Http\Controllers\AdminGraveController::class, 'mapManager'])->name('admin.map.manager');
});
<?php

use Illuminate\Support\Facades\Route;
use App\Models\Grave;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminGraveController;
use App\Http\Controllers\AdminDeceasedController;


// =========================================================================
// 1. PUBLIC ROUTES (Accessible by everyone)
// =========================================================================

// Homepage & Static Pages
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/contact', function () { return view('public.contact'); })->name('contact');

// Public Map (Fetches Data Directly)
Route::get('/map', function () {
    $graves = Grave::with(['section', 'deceased'])->get();
    return view('map', compact('graves'));
})->name('map.public');

// Search Functionality
Route::get('/search', [PublicController::class, 'search'])->name('grave.search');
Route::get('/directory', [PublicController::class, 'directory'])->name('public.directory');


// =========================================================================
// 2. ADMIN AUTHENTICATION (Guest Only)
// =========================================================================

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});


// =========================================================================
// 3. ADMIN DASHBOARD & MANAGEMENT (Login Required)
// =========================================================================

Route::middleware('auth:admin')->prefix('admin')->group(function () {

    // --- Dashboard & Auth ---
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [AdminGraveController::class, 'dashboard'])->name('admin.dashboard');
    // --- Module A: Grave Management ---
    Route::get('/map-manager', [AdminGraveController::class, 'mapManager'])->name('admin.map.manager');
    
    Route::get('/graves', [AdminGraveController::class, 'index'])->name('admin.graves.index');
    Route::get('/graves/create', [AdminGraveController::class, 'create'])->name('admin.graves.create');
    Route::post('/graves', [AdminGraveController::class, 'store'])->name('admin.graves.store');
    Route::get('/graves/{id}/edit', [AdminGraveController::class, 'edit'])->name('admin.graves.edit');
    Route::put('/graves/{id}', [AdminGraveController::class, 'update'])->name('admin.graves.update');
    Route::delete('/graves/{id}', [AdminGraveController::class, 'destroy'])->name('admin.graves.destroy');

    // --- Module B: Deceased Management ---
    Route::get('/deceased', [AdminDeceasedController::class, 'index'])->name('admin.deceased.index');
    Route::get('/deceased/create', [AdminDeceasedController::class, 'create'])->name('admin.deceased.create');
    Route::post('/deceased', [AdminDeceasedController::class, 'store'])->name('admin.deceased.store');
    Route::get('/deceased/{id}/edit', [AdminDeceasedController::class, 'edit'])->name('admin.deceased.edit');
    Route::put('/deceased/{id}', [AdminDeceasedController::class, 'update'])->name('admin.deceased.update');
    Route::delete('/deceased/{id}', [AdminDeceasedController::class, 'destroy'])->name('admin.deceased.destroy');

});
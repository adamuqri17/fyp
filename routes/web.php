<?php

use Illuminate\Support\Facades\Route;
use App\Models\Grave;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminGraveController;
use App\Http\Controllers\AdminDeceasedController;
use App\Http\Controllers\AdminLedgerController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\PublicLedgerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================
// 1. PUBLIC ROUTES
// =========================================================

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/contact', function () { return view('public.contact'); })->name('contact');

Route::get('/map', function () {
    $graves = Grave::with(['section', 'deceased'])->get();
    return view('map', compact('graves'));
})->name('map.public');

Route::get('/search', [PublicController::class, 'search'])->name('grave.search');
Route::get('/directory', [PublicController::class, 'directory'])->name('public.directory');

// Service & Orders
Route::get('/services', [PublicLedgerController::class, 'index'])->name('public.services.index');
Route::get('/services/order/{id}', [PublicLedgerController::class, 'create'])->name('public.ledgers.order');
Route::post('/services/order', [PublicLedgerController::class, 'store'])->name('public.ledgers.store');
Route::get('/services/search-deceased', [PublicLedgerController::class, 'searchDeceased'])->name('public.services.search');

// Payment
Route::get('/services/payment/return', [PublicLedgerController::class, 'paymentReturn'])->name('public.ledgers.return');
Route::post('/services/payment/callback', [PublicLedgerController::class, 'paymentCallback'])->name('public.ledgers.callback');
Route::get('/services/success', function() { return view('public.services.success'); })->name('public.services.success');


// =========================================================
// 2. ADMIN AUTHENTICATION
// =========================================================
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});

// =========================================================
// 3. ADMIN DASHBOARD & MODULES
// =========================================================
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AdminGraveController::class, 'dashboard'])->name('dashboard');
    Route::get('/map-manager', [AdminGraveController::class, 'mapManager'])->name('map.manager');
    
    Route::resource('graves', AdminGraveController::class);

    Route::get('/deceased/get-plots', [AdminDeceasedController::class, 'getPlots'])->name('deceased.get-plots');
    Route::resource('deceased', AdminDeceasedController::class);
    
    // UPDATED: Removed 'edit' and 'update' from except() to allow editing
    Route::resource('ledgers', AdminLedgerController::class)->except(['show']);

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{id}', [AdminOrderController::class, 'updateStatus'])->name('orders.update');
});
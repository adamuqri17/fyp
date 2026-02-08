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
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// =========================================================
// 1. PUBLIC ROUTES
// =========================================================

// Homepage & Static Pages
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/contact', function () { return view('public.contact'); })->name('contact');

// Public Interactive Map
Route::get('/map', function () {
    // Eager load relationships for performance
    $graves = Grave::with(['section', 'deceased'])->get();
    return view('map', compact('graves'));
})->name('map.public');

// Directory & Search
Route::get('/search', [PublicController::class, 'search'])->name('grave.search');
Route::get('/directory', [PublicController::class, 'directory'])->name('public.directory');

// --- HEADSTONE / LEDGER ORDERING SERVICES ---
Route::get('/services', [PublicLedgerController::class, 'index'])->name('public.services.index');

// Order Flow
Route::get('/services/order/{id}', [PublicLedgerController::class, 'create'])->name('public.ledgers.order');
Route::post('/services/order', [PublicLedgerController::class, 'store'])->name('public.ledgers.store'); // Redirects to ToyyibPay

// AJAX: Smart Search for Deceased/Plots (Used in Order Form)
Route::get('/services/search-deceased', [PublicLedgerController::class, 'searchDeceased'])->name('public.services.search');

// --- TOYYIBPAY PAYMENT ROUTES ---
Route::get('/services/payment/return', [PublicLedgerController::class, 'paymentReturn'])->name('public.ledgers.return');
Route::post('/services/payment/callback', [PublicLedgerController::class, 'paymentCallback'])->name('public.ledgers.callback');

// Success Confirmation Page
Route::get('/services/success', function() { 
    return view('public.services.success'); 
})->name('public.services.success');


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
    
    // Logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [AdminGraveController::class, 'dashboard'])->name('dashboard');
    
    // Map Manager (Visual Editor)
    Route::get('/map-manager', [AdminGraveController::class, 'mapManager'])->name('map.manager');
    
    // Core Resource Management
    Route::resource('graves', AdminGraveController::class);

    // CRITICAL FIX: Define this route BEFORE Route::resource('deceased')
    // This prevents "get-plots" from being treated as an {id} in the Show route.
    Route::get('/deceased/get-plots', [AdminDeceasedController::class, 'getPlots'])->name('deceased.get-plots');
    
    // Now define the resource
    Route::resource('deceased', AdminDeceasedController::class);
    
    // Ledger Catalog Management
    Route::resource('ledgers', AdminLedgerController::class)->except(['show', 'edit', 'update']);

    // Order Management (View & Update Status)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{id}', [AdminOrderController::class, 'updateStatus'])->name('orders.update');
});
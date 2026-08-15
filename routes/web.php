<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ValuationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Public\WebsiteController;
use Illuminate\Support\Facades\Route;

// ── Public website ────────────────────────────────────────────────────────────
Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/about', [WebsiteController::class, 'about'])->name('about');
Route::get('/services', [WebsiteController::class, 'services'])->name('services');
Route::get('/contact', [WebsiteController::class, 'contact'])->name('contact');
Route::post('/inquiry', [WebsiteController::class, 'submitInquiry'])->name('inquiry.submit');

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients
    Route::resource('clients', ClientController::class)->except(['destroy']);

    // Properties
    Route::resource('properties', PropertyController::class)->except(['destroy']);

    // Valuations
    Route::get('/valuations', [ValuationController::class, 'index'])->name('valuations.index');
    Route::get('/valuations/create', [ValuationController::class, 'create'])->name('valuations.create');
    Route::post('/valuations', [ValuationController::class, 'store'])->name('valuations.store');
    Route::get('/valuations/{valuation}', [ValuationController::class, 'show'])->name('valuations.show');
    Route::patch('/valuations/{valuation}/status', [ValuationController::class, 'updateStatus'])->name('valuations.update-status');
    Route::post('/valuations/{valuation}/reports', [ValuationController::class, 'storeReport'])->name('valuations.reports.store');

    // Staff
    Route::resource('staff', StaffController::class)->except(['show', 'destroy']);

    // Settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});


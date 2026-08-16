<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::inertia('/annex3', 'Annex3')->name('annex3');
use App\Http\Controllers\ValuationRequestController;

Route::get('/annex-c', [ValuationRequestController::class, 'create'])
    ->name('annex-c.create');

Route::post('/annex-c', [ValuationRequestController::class, 'store'])
    ->name('annex-c.store');
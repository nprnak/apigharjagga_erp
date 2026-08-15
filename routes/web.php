<?php

use App\Http\Controllers\PropertyListingController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/property-listing', [PropertyListingController::class, 'index'])->name('property.listing');
Route::post('/property-listing', [PropertyListingController::class, 'store'])->name('property.listing.store');
Route::get('/property-listing/{id}/pdf', [PropertyListingController::class, 'downloadPdf'])->name('property.listing.pdf');

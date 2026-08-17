<?php

use App\Http\Controllers\AgreementController;
use App\Http\Controllers\ClientRegistrationController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PropertyListingController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/property-listing', [PropertyListingController::class, 'index'])->name('property.listing');
Route::post('/property-listing', [PropertyListingController::class, 'store'])->name('property.listing.store');
Route::get('/property-listing/{id}/pdf', [PropertyListingController::class, 'downloadPdf'])->name('property.listing.pdf');

Route::get('/agreement', [AgreementController::class, 'index'])->name('agreement.form');
Route::post('/agreement', [AgreementController::class, 'store'])->name('agreement.store');
Route::get('/agreement/{id}/pdf', [AgreementController::class, 'downloadPdf'])->name('agreement.pdf');

Route::get('/client-registration', [ClientRegistrationController::class, 'index'])->name('client.registration');
Route::post('/client-registration', [ClientRegistrationController::class, 'store'])->name('client.registration.store');
Route::get('/client-registration/{id}/pdf', [ClientRegistrationController::class, 'downloadPdf'])->name('client.registration.pdf');

Route::get('/complaint', [ComplaintController::class, 'index'])->name('complaint.form');
Route::post('/complaint', [ComplaintController::class, 'store'])->name('complaint.store');
Route::get('/complaint/{id}/pdf', [ComplaintController::class, 'downloadPdf'])->name('complaint.pdf');

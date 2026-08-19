<?php

use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\ClientRegistrationController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\KycController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyListingController;
use App\Http\Controllers\ValuationRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketplaceController::class, 'landing'])->name('home');

// Public marketplace pages
Route::get('/properties', [MarketplaceController::class, 'index'])->name('properties.index');

Route::get('/property-listing', [PropertyListingController::class, 'index'])->name('property.listing');
Route::post('/property-listing', [PropertyListingController::class, 'store'])->name('property.listing.store');
Route::get('/property-listing/{id}/pdf', [PropertyListingController::class, 'downloadPdf'])->name('property.listing.pdf');

// Breeze auth (login / register / password reset)
Route::get('/signin', fn () => redirect()->route('login'))->name('signin');
Route::get('/signup', fn () => redirect()->route('register'))->name('signup');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
});

// Named route used by Breeze auth redirects and KYC/property controllers.
// Maps legacy ?tab= query params to the Filament user panel pages.
Route::middleware('auth')->get('/user/dashboard', function (Request $request) {
    $target = match ($request->query('tab')) {
        'kyc' => '/dashboard/kyc-verification-page',
        'listings' => '/dashboard/my-properties',
        default => '/dashboard',
    };

    return redirect($target);
})->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/admin/approve', [AdminApprovalController::class, 'approve'])->name('admin.approve');
    Route::post('/admin/reject', [AdminApprovalController::class, 'reject'])->name('admin.reject');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/agreement', [AgreementController::class, 'index'])->name('agreement.form');
Route::post('/agreement', [AgreementController::class, 'store'])->name('agreement.store');
Route::get('/agreement/{id}/pdf', [AgreementController::class, 'downloadPdf'])->name('agreement.pdf');

Route::get('/client-registration', [ClientRegistrationController::class, 'index'])->name('client.registration');
Route::post('/client-registration', [ClientRegistrationController::class, 'store'])->name('client.registration.store');
Route::get('/client-registration/{id}/pdf', [ClientRegistrationController::class, 'downloadPdf'])->name('client.registration.pdf');

Route::get('/complaint', [ComplaintController::class, 'index'])->name('complaint.form');
Route::post('/complaint', [ComplaintController::class, 'store'])->name('complaint.store');
Route::get('/complaint/{id}/pdf', [ComplaintController::class, 'downloadPdf'])->name('complaint.pdf');

Route::inertia('/annex3', 'Annex3')->name('annex3');

Route::get('/annex-c', [ValuationRequestController::class, 'create'])
    ->name('annex-c.create');

Route::post('/annex-c', [ValuationRequestController::class, 'store'])
    ->name('annex-c.store');

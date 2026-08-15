<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/property-listing', 'PropertyListing/PropertyListingForm')->name('property.listing');

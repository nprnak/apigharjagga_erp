<?php

namespace App\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Unified logout handler for both Filament panels.
 *
 * Both panels use the shared /login entry point, so logout must clear both
 * guards and return to that page rather than redirecting to a panel URL.
 */
class FilamentLogoutController
{
    public function __invoke(): RedirectResponse
    {
        Filament::auth()->logout();
        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }
}

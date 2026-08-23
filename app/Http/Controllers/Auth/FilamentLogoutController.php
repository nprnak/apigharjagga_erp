<?php

namespace App\Http\Controllers\Auth;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse;
use Filament\Facades\Filament;

/**
 * Guard-scoped replacement for Filament's default logout controller.
 *
 * Filament's built-in controller calls session()->invalidate(), which flushes
 * the entire session and therefore signs out every guard sharing that session
 * (both the admin and the user panels). This version logs out only the current
 * panel's guard and uses regenerate() instead, so signing out of one panel
 * leaves a session established on the other panel intact.
 */
class FilamentLogoutController
{
    public function __invoke(): LogoutResponse
    {
        // Filament::auth() resolves the guard configured for the current panel
        // (the "admin" guard on the admin panel, "web" on the user panel).
        Filament::auth()->logout();

        // regenerate() rotates the session id and CSRF token (matching the
        // security posture of invalidate()) but preserves the data belonging
        // to any other guard still authenticated in this browser.
        session()->regenerate();

        return app(LogoutResponse::class);
    }
}

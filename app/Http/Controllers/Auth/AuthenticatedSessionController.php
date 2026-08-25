<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * Uses Inertia::location() because the post-login destination is the
     * Filament user panel (non-Inertia HTML). A normal redirect would make
     * Inertia v3 open that HTML in a floating error <dialog> on top of /login.
     */
    public function store(LoginRequest $request): SymfonyResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return Inertia::location(
            $request->session()->pull('url.intended', url('/dashboard'))
        );
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log out only the web guard. Using regenerate() (instead of
        // invalidate()) rotates the session id and CSRF token for security
        // while preserving any separate admin-panel session in the same
        // browser, so signing out here does not evict a logged-in admin.
        Auth::guard('web')->logout();

        $request->session()->regenerate();

        return redirect('/');
    }
}

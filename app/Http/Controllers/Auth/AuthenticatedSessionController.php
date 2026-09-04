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
     * Uses Inertia::location() because the post-login destination may be the
     * Filament admin/user panel (non-Inertia HTML). A normal redirect would make
     * Inertia v3 open that HTML in a floating error <dialog> on top of /login.
     */
    public function store(LoginRequest $request): SymfonyResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::guard('web')->user();

        // Single platform login: admins land on the admin panel, everyone
        // else goes to their intended page or the user dashboard.
        $target = $user->role === 'admin'
            ? url('/admin')
            : $request->session()->pull('url.intended', url('/dashboard'));

        return Inertia::location($target);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log out both guards since a single login form controls both the
        // user and admin panel sessions.
        Auth::guard('web')->logout();
        Auth::guard('admin')->logout();

        $request->session()->regenerate();

        return redirect('/');
    }
}

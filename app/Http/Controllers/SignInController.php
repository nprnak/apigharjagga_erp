<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SignInController extends Controller
{
    public function show(Request $request): InertiaResponse
    {
        return Inertia::render('Auth/SignIn', [
            'canResetPassword' => false,
            'status' => null,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email:rfc,dns', 'max:255'],
                'password' => ['required', 'string', 'min:1'],
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors([
                    'email' => __('These credentials do not match our records.'),
                ])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended('/properties');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/');
    }
}


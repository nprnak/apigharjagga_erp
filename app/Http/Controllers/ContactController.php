<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ContactController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('Contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string'],
        ]);

        ContactMessage::create([
            ...$validated,
            'status' => 'new',
        ]);

        return back()->with('success', 'Thank you for reaching out. We will get back to you shortly.');
    }
}

<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WebsiteController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Public/Home');
    }

    public function about(): Response
    {
        return Inertia::render('Public/About');
    }

    public function services(): Response
    {
        return Inertia::render('Public/Services');
    }

    public function contact(): Response
    {
        return Inertia::render('Public/Contact');
    }

    public function submitInquiry(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'email'   => ['nullable', 'email'],
            'phone'   => ['required', 'string', 'max:20'],
            'service' => ['nullable', 'string'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        // Store inquiry in client_service_requests when they exist
        // For now, send email notification (mail driver defaults to log)

        return back()->with('success', 'Your inquiry has been submitted. We will contact you shortly.');
    }
}

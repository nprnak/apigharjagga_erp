<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\Testimonial;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class AboutController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('About', [
            'team' => TeamMember::where('is_active', true)
                ->orderBy('display_order')
                ->get(['member_id', 'name', 'designation', 'department', 'photo_path', 'bio']),
            'testimonials' => Testimonial::where('is_active', true)
                ->orderBy('display_order')
                ->get(['testimonial_id', 'client_name', 'client_role', 'client_photo_path', 'rating', 'message', 'is_featured']),
        ]);
    }
}

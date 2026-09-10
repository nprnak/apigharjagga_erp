<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class GalleryController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('Gallery', [
            'albums' => GalleryAlbum::where('is_active', true)
                ->with(['images' => fn ($q) => $q->orderBy('display_order')])
                ->orderByDesc('created_at')
                ->get(['album_id', 'title', 'description', 'cover_image_path']),
        ]);
    }
}

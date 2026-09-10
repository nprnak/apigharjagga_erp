<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BlogController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('Blog/Index', [
            'posts' => BlogPost::where('is_published', true)
                ->orderByDesc('published_at')
                ->get(['post_id', 'title', 'slug', 'excerpt', 'cover_image_path', 'category', 'published_at']),
        ]);
    }

    public function show(string $slug): InertiaResponse
    {
        $post = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->with('author')
            ->firstOrFail();

        $related = BlogPost::where('is_published', true)
            ->where('post_id', '!=', $post->post_id)
            ->when($post->category, fn ($q) => $q->where('category', $post->category))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['post_id', 'title', 'slug', 'cover_image_path', 'published_at']);

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'related' => $related,
        ]);
    }
}

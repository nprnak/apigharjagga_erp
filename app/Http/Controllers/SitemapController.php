<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\PropertyListing;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;

class SitemapController extends Controller
{
    /**
     * A hand-rolled XML sitemap rather than a package: the site's public
     * surface is small and static (a handful of marketing pages) plus two
     * dynamic sources (published blog posts, live property listings).
     */
    public function index(): Response
    {
        $staticUrls = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('properties.index'), 'priority' => '0.9'],
            ['loc' => route('about'), 'priority' => '0.7'],
            ['loc' => route('careers'), 'priority' => '0.6'],
            ['loc' => route('blog.index'), 'priority' => '0.7'],
            ['loc' => route('gallery'), 'priority' => '0.5'],
            ['loc' => route('documents'), 'priority' => '0.4'],
            ['loc' => route('contact'), 'priority' => '0.6'],
        ];

        $blogUrls = BlogPost::where('is_published', true)
            ->get(['slug', 'updated_at'])
            ->map(fn (BlogPost $post) => [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at?->toAtomString(),
                'priority' => '0.6',
            ]);

        $propertyUrls = PropertyListing::whereHas('property', fn ($q) => $q->where('is_listed', true))
            ->with('property')
            ->get()
            ->map(fn (PropertyListing $listing) => [
                'loc' => route('properties.show', $listing->listing_id),
                'lastmod' => $listing->updated_at->toAtomString(),
                'priority' => '0.8',
            ]);

        $urls = collect($staticUrls)->concat($blogUrls)->concat($propertyUrls);

        $xml = Blade::render(
            <<<'BLADE'
                <?xml version="1.0" encoding="UTF-8"?>
                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
                @foreach ($urls as $url)
                    <url>
                        <loc>{{ $url['loc'] }}</loc>
                        @isset($url['lastmod'])
                            <lastmod>{{ $url['lastmod'] }}</lastmod>
                        @endisset
                        <priority>{{ $url['priority'] }}</priority>
                    </url>
                @endforeach
                </urlset>
                BLADE,
            ['urls' => $urls],
        );

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}

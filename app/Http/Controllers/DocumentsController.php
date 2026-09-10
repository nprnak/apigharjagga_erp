<?php

namespace App\Http\Controllers;

use App\Models\SiteDocument;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DocumentsController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('Documents', [
            'documents' => SiteDocument::where('is_public', true)
                ->orderByDesc('uploaded_at')
                ->get(['document_id', 'title', 'category', 'file_path', 'uploaded_at']),
        ]);
    }
}

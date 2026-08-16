<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ValuationRequestController extends Controller
{
    public function create()
    {
        return Inertia::render('AnnexC');
    }

    public function store(Request $request)
    {
        return back()->with('success', 'Valuation request received successfully.');
    }
}
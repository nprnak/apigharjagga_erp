<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Property;
use App\Models\ValuationRequest;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_clients'     => Client::count(),
            'total_properties'  => Property::count(),
            'pending_valuations' => ValuationRequest::where('status', 'received')->count(),
            'in_progress_valuations' => ValuationRequest::where('status', 'in_progress')->count(),
            'properties_by_type' => Property::selectRaw('property_type, count(*) as count')
                ->groupBy('property_type')
                ->pluck('count', 'property_type'),
            'recent_clients' => Client::latest('created_at')->limit(5)->get([
                'client_id', 'client_code', 'full_name', 'client_type', 'mobile_no', 'created_at',
            ]),
            'recent_valuations' => ValuationRequest::with(['client:client_id,full_name', 'property:property_id,property_code'])
                ->latest('created_at')
                ->limit(5)
                ->get(['request_id', 'request_code', 'client_id', 'property_id', 'status', 'created_at']),
        ];

        return Inertia::render('Admin/Dashboard', ['stats' => $stats]);
    }
}

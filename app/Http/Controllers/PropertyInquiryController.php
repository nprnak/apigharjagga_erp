<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyInquiry;
use App\Models\PropertyListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropertyInquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'property_id' => ['required', 'integer', Rule::exists('properties', 'property_id')],
            'listing_id' => ['nullable', 'integer', Rule::exists('property_listings', 'listing_id')],
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        // Guard against inquiries on listings that aren't actually linked to the given property.
        if (! empty($data['listing_id'])) {
            $belongsToProperty = PropertyListing::query()
                ->where('listing_id', $data['listing_id'])
                ->where('property_id', $data['property_id'])
                ->exists();

            if (! $belongsToProperty) {
                $data['listing_id'] = null;
            }
        }

        $inquiry = PropertyInquiry::create([
            'property_id' => $data['property_id'],
            'listing_id' => $data['listing_id'] ?? null,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'message' => $data['message'] ?? null,
            'status' => 'new',
        ]);

        return response()->json([
            'success' => true,
            'inquiry_id' => $inquiry->inquiry_id,
            'message' => 'Thank you! Our representative will contact you shortly.',
        ], 201);
    }
}

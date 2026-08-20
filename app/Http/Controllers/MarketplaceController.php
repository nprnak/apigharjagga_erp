<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyListing;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class MarketplaceController extends Controller
{
    /**
     * Nepal metropolitan (Mahanagarpalika) city names.
     * Source (for naming): matches common English spellings used in listings.
     */
    private const METROPOLITAN_CITIES = [
        'Kathmandu',
        'Pokhara',
        'Bharatpur',
        'Biratnagar',
        'Lalitpur',
        'Birgunj',
    ];

    /**
     * Nepal sub-metropolitan (Upa-Mahanagarpalika) city names.
     */
    private const SUB_METROPOLITAN_CITIES = [
        'Dharan',
        'Itahari',
        'Janakpur',
        'Jitpur-Simara',
        'Kalaiya',
        'Hetauda',
        'Butwal',
        'Ghorahi',
        'Tulsipur',
        'Nepalgunj',
        'Dhangadhi',
    ];

    private function cityOptions(): array
    {
        $options = [];

        foreach (self::METROPOLITAN_CITIES as $city) {
            $options[] = [
                'value' => $city,
                'label' => $city . ' Metropolitan City',
                'type' => 'metropolitan',
            ];
        }

        foreach (self::SUB_METROPOLITAN_CITIES as $city) {
            $options[] = [
                'value' => $city,
                'label' => $city . ' Sub-Metropolitan City',
                'type' => 'sub-metropolitan',
            ];
        }

        return $options;
    }

    public function landing(Request $request): InertiaResponse
    {
        $featuredListings = $this->featuredQuery(limit: 8)->get();

        return Inertia::render('Marketplace/MarketplaceLanding', [
            'featuredListings' => $this->transformListings($featuredListings),
            'locations' => $this->locationDirectory(),
            'cityOptions' => $this->cityOptions(),
        ]);
    }

    /**
     * Latest approved/active listings for the homepage carousel.
     */
    private function featuredQuery(int $limit)
    {
        $limit = max(1, min($limit, 24));

        return PropertyListing::query()
            ->with([
                'property:property_id,property_code,property_type,area,covered_area,no_of_floors,status,address_id',
                'property.address:address_id,municipality,district,province',
                'property.photos',
            ])
            ->where(function ($q) {
                $q->where('listing_status', 'approved')
                    ->orWhereNull('listing_status')
                    ->orWhere('listing_status', 'listed');
            })
            ->whereHas('property', function ($q) {
                $q->whereIn('approval_status', ['approved', 'pending'])
                    ->orWhereIn('status', ['listed', 'draft']);
            })
            ->orderByRaw("CASE WHEN listing_status = 'approved' THEN 0 ELSE 1 END")
            ->orderByDesc('listing_id')
            ->limit($limit);
    }

    /**
     * Province → city directory derived from real address rows on listed properties.
     * No new table is introduced; data comes straight from the existing addresses table.
     */
    private function locationDirectory(): array
    {
        $rows = Property::query()
            ->join('addresses', 'properties.address_id', '=', 'addresses.address_id')
            ->whereNotNull('addresses.province')
            ->whereNotNull('addresses.municipality')
            ->where('addresses.province', '!=', '')
            ->where('addresses.municipality', '!=', '')
            ->selectRaw('addresses.province as province, addresses.municipality as municipality, COUNT(*) as cnt')
            ->groupBy('addresses.province', 'addresses.municipality')
            ->get();

        return $rows
            ->groupBy('province')
            ->map(fn ($group, $province) => [
                'province' => (string) $province,
                'cities' => $group
                    ->map(fn ($row) => [
                        'name' => (string) $row->municipality,
                        'count' => (int) $row->cnt,
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    public function index(Request $request): InertiaResponse
    {
        $listings = $this->queryListings($request, limit: 24)->get();

        return Inertia::render('Marketplace/MarketplaceIndex', [
            'listings' => $this->transformListings($listings),
            'cityOptions' => $this->cityOptions(),
            'filters' => [
                'q' => (string) $request->query('q', ''),
                'city' => (string) $request->query('city', ''),
                'purpose' => (string) $request->query('purpose', ''),
            ],
        ]);
    }

    private function queryListings(Request $request, int $limit)
    {
        $q = trim((string) $request->query('q', ''));
        $city = trim((string) $request->query('city', ''));
        $purpose = trim((string) $request->query('purpose', ''));

        $limit = max(1, min($limit, 100));

        $query = PropertyListing::query()
            ->with([
                'property:property_id,property_code,property_type,area,covered_area,no_of_floors,status,address_id',
                'property.address:address_id,municipality,district,province',
                'property.photos',
            ])
            ->where(function ($q2) {
                $q2->where('listing_status', 'approved')
                    ->orWhereNull('listing_status')
                    ->orWhere('listing_status', 'listed');
            })
            ->whereHas('property', function ($q2) {
                $q2->whereIn('approval_status', ['approved', 'pending'])
                    ->orWhereIn('status', ['listed', 'draft']);
            })
            ->orderByRaw("CASE WHEN listing_status = 'approved' THEN 0 ELSE 1 END")
            ->orderByDesc('listing_id')
            ->limit($limit);

        if ($purpose !== '') {
            $query->where('purpose_of_listing', $purpose);
        }

        if ($city !== '') {
            // Match municipality case-insensitively because the listing form stores free text.
            $query->whereHas('property.address', function ($q2) use ($city) {
                $q2->whereRaw('LOWER(municipality) LIKE LOWER(?)', ['%' . $city . '%']);
            });
        }

        if ($q !== '') {
            $query->where(function ($q2) use ($q) {
                $q2->where('application_no', 'like', '%' . $q . '%')
                    ->orWhereHas('property', function ($p) use ($q) {
                        $p->where('property_code', 'like', '%' . $q . '%');
                    })
                    ->orWhereHas('property.address', function ($a) use ($q) {
                        $a->where('municipality', 'like', '%' . $q . '%');
                    });
            });
        }

        return $query;
    }

    private function transformListings($listings): array
    {
        return $listings->map(function (PropertyListing $listing) {
            $property = $listing->property;
            $address = $property?->address;

            $price = null;
            if ($listing->purpose_of_listing === 'rent') {
                $price = $listing->rental_amount ?? null;
            } else {
                $price = $listing->expected_selling_price
                    ?? $listing->minimum_acceptable_price
                    ?? null;
            }

            $photos = $property?->photos?->map(fn ($p) => $p->photo_url)->filter()->values()->toArray() ?? [];
            $photoUrl = $photos[0] ?? null;

            return [
                'listing_id' => $listing->listing_id,
                'application_no' => $listing->application_no,
                'purpose' => $listing->purpose_of_listing,
                'price' => $price,
                'negotiable' => (bool) $listing->negotiable,
                'property_code' => $property?->property_code,
                'property_type' => $property?->property_type,
                'area' => $property?->area,
                'covered_area' => $property?->covered_area,
                'no_of_floors' => $property?->no_of_floors,
                'status' => $property?->status,
                'municipality' => $address?->municipality,
                'district' => $address?->district,
                'province' => $address?->province,
                'photo_url' => $photoUrl,
                'photos' => $photos,
            ];
        })->values()->toArray();
    }
}


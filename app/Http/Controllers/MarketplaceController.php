<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyListing;
use Illuminate\Http\RedirectResponse;
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
     * Base query for listings that are safe to expose on the public marketplace
     * (used by the featured carousel, the search grid, and the detail page so
     * that a raw listing/property ID can't be guessed to view a hidden record).
     */
    private function visibleListingsQuery()
    {
        return PropertyListing::query()
            ->where(function ($q) {
                $q->where('listing_status', 'approved')
                    ->orWhereNull('listing_status')
                    ->orWhere('listing_status', 'listed');
            })
            ->whereHas('property', function ($q) {
                $q->whereIn('approval_status', ['approved', 'pending'])
                    ->orWhereIn('status', ['listed', 'draft']);
            });
    }

    /**
     * Latest approved/active listings for the homepage carousel.
     */
    private function featuredQuery(int $limit)
    {
        $limit = max(1, min($limit, 24));

        return $this->visibleListingsQuery()
            ->with([
                'property:property_id,property_code,property_type,area,covered_area,no_of_floors,status,address_id',
                'property.address:address_id,municipality,district,province',
                'property.photos',
            ])
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

    /**
     * Public property detail page. Only listings that pass the same visibility
     * rules as the search grid/carousel can be viewed here.
     */
    public function show(int $listing): InertiaResponse|RedirectResponse
    {
        $propertyListing = $this->visibleListingsQuery()
            ->with([
                'property.address',
                'property.photos',
            ])
            ->find($listing);

        if (! $propertyListing) {
            return redirect()
                ->route('properties.index')
                ->with('notice', 'That listing is no longer available.');
        }

        return Inertia::render('Marketplace/PropertyDetail', [
            'listing' => $this->transformListingDetail($propertyListing),
        ]);
    }

    private function queryListings(Request $request, int $limit)
    {
        $q = trim((string) $request->query('q', ''));
        $city = trim((string) $request->query('city', ''));
        $purpose = trim((string) $request->query('purpose', ''));

        $limit = max(1, min($limit, 100));

        $query = $this->visibleListingsQuery()
            ->with([
                'property:property_id,property_code,property_type,area,covered_area,no_of_floors,status,address_id',
                'property.address:address_id,municipality,district,province',
                'property.photos',
            ])
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

    /**
     * Full dynamic payload for the property detail page. Deliberately omits
     * legally-sensitive identifiers (ownership certificate / building permit
     * numbers) and the owner's personal contact details, which are not
     * appropriate to expose to anonymous marketplace visitors.
     */
    private function transformListingDetail(PropertyListing $listing): array
    {
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

        return [
            'listing_id' => $listing->listing_id,
            'application_no' => $listing->application_no,
            'purpose' => $listing->purpose_of_listing,
            'price' => $price,
            'negotiable' => (bool) $listing->negotiable,
            'legal_verification_status' => $listing->legal_verification_status,
            'remarks' => $listing->remarks,

            'property_id' => $property?->property_id,
            'property_code' => $property?->property_code,
            'property_type' => $property?->property_type,
            'area' => $property?->area,
            'covered_area' => $property?->covered_area,
            'no_of_floors' => $property?->no_of_floors,
            'status' => $property?->status,
            'kitta_no' => $property?->kitta_no,
            'map_sheet_no' => $property?->map_sheet_no,
            'ownership_type' => $property?->ownership_type,
            'road_access' => $property?->road_access,
            'road_width' => $property?->road_width,
            'facing_direction' => $property?->facing_direction,
            'year_of_construction' => $property?->year_of_construction,
            'structure_type' => $property?->structure_type,
            'roof_type' => $property?->roof_type,
            'parking' => $property?->parking,
            'water_supply' => $property?->water_supply,
            'electricity' => $property?->electricity,
            'internet' => $property?->internet,
            'drainage' => $property?->drainage,
            'current_building_condition' => $property?->current_building_condition,

            'municipality' => $address?->municipality,
            'district' => $address?->district,
            'province' => $address?->province,
            'ward_no' => $address?->ward_no,
            'tole_locality' => $address?->tole_locality,
            'full_address_text' => $address?->full_address_text,

            'photo_url' => $photos[0] ?? null,
            'photos' => $photos,
        ];
    }
}


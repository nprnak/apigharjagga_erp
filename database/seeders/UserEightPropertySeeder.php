<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyListing;
use App\Models\PropertyPhoto;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserEightPropertySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(8);

        if (! $user) {
            $this->command?->warn('User 8 was not found; no property was seeded.');

            return;
        }

        $client = Client::firstOrCreate(
            ['mobile_app_user_id' => (string) $user->id],
            [
                'client_code' => 'CLT-U' . $user->id,
                'client_type' => 'owner',
                'full_name' => $user->name,
                'email' => $user->email,
                'mobile_no' => '9812345678',
                'registration_date' => now()->toDateString(),
                'mis_entry_status' => 'completed',
                'is_active' => true,
            ]
        );

        $address = Address::updateOrCreate(
            [
                'province' => 'Bagmati',
                'district' => 'Kathmandu',
                'municipality' => 'Budhanilkantha Municipality',
                'ward_no' => '03',
                'tole_locality' => 'Sampang Chowk, Hattigauda',
            ],
            [
                'full_address_text' => 'Sampang Chowk, Hattigauda, Budhanilkantha-03, Kathmandu',
            ]
        );

        $property = Property::updateOrCreate(
            ['property_code' => 'PROP-KTM-SUNDAR-001'],
            [
                'owner_client_id' => $client->client_id,
                'user_id' => $user->id,
                'ownership_role' => 'self',
                'property_type' => 'land',
                'address_id' => $address->address_id,
                'kitta_no' => '1278',
                'area' => '0-8-0-0 (8 Aana / approximately 2,736 sq.ft)',
                'map_sheet_no' => '102-0786-12',
                'ownership_type' => 'private',
                'ownership_certificate_no' => 'LAL-KTM-45821',
                'road_access' => 'blacktopped',
                'road_width' => '20 ft',
                'facing_direction' => 'South-East',
                'status' => 'listed',
                'approval_status' => 'approved',
                'is_listed' => true,
            ]
        );

        $listing = PropertyListing::updateOrCreate(
            ['application_no' => 'AGJ-2026-U8-0001'],
            [
                'property_id' => $property->property_id,
                'applicant_client_id' => $client->client_id,
                'purpose_of_listing' => 'sale',
                'expected_selling_price' => 32000000,
                'negotiable' => true,
                'date_received' => now()->toDateString(),
                'effective_date' => now()->toDateString(),
                'inspection_required' => false,
                'valuation_required' => false,
                'photographs_received' => true,
                'gis_location_verified' => false,
                'legal_verification_status' => 'completed',
                'listing_status' => 'approved',
                'remarks' => 'Hattigauda South-East Corner Residential Land',
            ]
        );

        $photos = [
            ['front', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1400&q=85', 'Open view of the residential plot'],
            ['boundary', 'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1400&q=85', 'Green boundary and fertile soil'],
            ['surrounding', 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=1400&q=85', 'Quiet neighbourhood surroundings'],
            ['road_access', 'https://images.unsplash.com/photo-1510798831971-661eb04b3739?auto=format&fit=crop&w=1400&q=85', 'Road access near the property'],
            ['side', 'https://images.unsplash.com/photo-1449157291145-7ceeda5ccf7b?auto=format&fit=crop&w=1400&q=85', 'Nearby residential development'],
            ['surrounding', 'https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&w=1400&q=85', 'Hillside landscape around Kathmandu'],
            ['boundary', 'https://images.unsplash.com/photo-1473445361085-b9a07f55608b?auto=format&fit=crop&w=1400&q=85', 'Tree-lined edge of the land'],
            ['surrounding', 'https://images.unsplash.com/photo-1493246507139-91e8fad9978e?auto=format&fit=crop&w=1400&q=85', 'Green belt close to the plot'],
            ['road_access', 'https://images.unsplash.com/photo-1534777367038-9404f45b869a?auto=format&fit=crop&w=1400&q=85', 'Access road and local amenities'],
            ['front', 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1400&q=85', 'Ideal setting for a family home'],
        ];

        PropertyPhoto::where('property_id', $property->property_id)->delete();

        foreach ($photos as $photo) {
            PropertyPhoto::create([
                'property_id' => $property->property_id,
                'source_type' => 'listing',
                'source_id' => $listing->listing_id,
                'photo_type' => $photo[0],
                'file_ref' => $photo[1],
                'caption' => $photo[2],
                'uploaded_at' => now(),
            ]);
        }
    }
}
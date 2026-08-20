<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyListing;
use App\Models\PropertyPhoto;
use Illuminate\Database\Seeder;

class SamplePropertiesSeeder extends Seeder
{
    /**
     * Run the database seeds to populate realistic marketplace properties with 5 photos for test property.
     */
    public function run(): void
    {
        // 1. Get or create a sample owner client
        $client = Client::firstOrCreate(
            ['client_code' => 'CLI-AGJ-0001'],
            [
                'client_type' => 'owner',
                'full_name' => 'Api Ghar Jagga Properties',
                'mobile_no' => '9800000000',
                'email' => 'info@apigharjagga.com',
                'nationality' => 'Nepali',
                'is_active' => true,
            ]
        );

        $propertiesData = [
            [
                'property_code' => 'PROP-KTM-001',
                'application_no' => 'APP-2026-0101',
                'property_type' => 'house',
                'purpose' => 'sale',
                'price' => 35000000,
                'negotiable' => true,
                'no_of_floors' => 3,
                'area' => '0-4-2-0 Aana',
                'covered_area' => '2,400 sq.ft',
                'municipality' => 'Kathmandu',
                'district' => 'Kathmandu',
                'province' => 'Bagmati',
                'tole' => 'Baluwatar',
                'photos' => [
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80', // Exterior Front
                    'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1200&q=80', // Living Room
                    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1200&q=80', // Modern Kitchen & Dining
                    'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?auto=format&fit=crop&w=1200&q=80', // Master Bedroom
                    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80', // Balcony / Terrace Garden
                ],
            ],
            [
                'property_code' => 'PROP-LAL-002',
                'application_no' => 'APP-2026-0102',
                'property_type' => 'apartment',
                'purpose' => 'sale',
                'price' => 18500000,
                'negotiable' => false,
                'no_of_floors' => 1,
                'area' => '1,350 sq.ft',
                'covered_area' => '1,350 sq.ft',
                'municipality' => 'Lalitpur',
                'district' => 'Lalitpur',
                'province' => 'Bagmati',
                'tole' => 'Jhamsikhel',
                'photos' => [
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'property_code' => 'PROP-PKR-003',
                'application_no' => 'APP-2026-0103',
                'property_type' => 'house',
                'purpose' => 'sale',
                'price' => 27500000,
                'negotiable' => true,
                'no_of_floors' => 2,
                'area' => '0-6-0-0 Aana',
                'covered_area' => '2,100 sq.ft',
                'municipality' => 'Pokhara',
                'district' => 'Kaski',
                'province' => 'Gandaki',
                'tole' => 'Lakeside',
                'photos' => [
                    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1200&q=80',
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'property_code' => 'PROP-BHP-004',
                'application_no' => 'APP-2026-0104',
                'property_type' => 'commercial_building',
                'purpose' => 'rent',
                'price' => 120000,
                'negotiable' => true,
                'no_of_floors' => 4,
                'area' => '0-8-0-0 Aana',
                'covered_area' => '4,500 sq.ft',
                'municipality' => 'Bharatpur',
                'district' => 'Chitwan',
                'province' => 'Bagmati',
                'tole' => 'Narayangarh',
                'photos' => [
                    'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'property_code' => 'PROP-KTM-005',
                'application_no' => 'APP-2026-0105',
                'property_type' => 'house',
                'purpose' => 'rent',
                'price' => 65000,
                'negotiable' => false,
                'no_of_floors' => 2,
                'area' => '0-5-0-0 Aana',
                'covered_area' => '1,800 sq.ft',
                'municipality' => 'Kathmandu',
                'district' => 'Kathmandu',
                'province' => 'Bagmati',
                'tole' => 'Budhanilkantha',
                'photos' => [
                    'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'property_code' => 'PROP-DHN-006',
                'application_no' => 'APP-2026-0106',
                'property_type' => 'land',
                'purpose' => 'sale',
                'price' => 9500000,
                'negotiable' => true,
                'no_of_floors' => null,
                'area' => '0-10-0-0 Aana',
                'covered_area' => null,
                'municipality' => 'Dharan',
                'district' => 'Sunsari',
                'province' => 'Koshi',
                'tole' => 'Bhanuchowk',
                'photos' => [
                    'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'property_code' => 'PROP-BTW-007',
                'application_no' => 'APP-2026-0107',
                'property_type' => 'house',
                'purpose' => 'sale',
                'price' => 21000000,
                'negotiable' => true,
                'no_of_floors' => 2,
                'area' => '0-5-2-0 Aana',
                'covered_area' => '1,950 sq.ft',
                'municipality' => 'Butwal',
                'district' => 'Rupandehi',
                'province' => 'Lumbini',
                'tole' => 'Traffic Chowk',
                'photos' => [
                    'https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            [
                'property_code' => 'PROP-LAL-008',
                'application_no' => 'APP-2026-0108',
                'property_type' => 'office_space',
                'purpose' => 'rent',
                'price' => 85000,
                'negotiable' => false,
                'no_of_floors' => 1,
                'area' => '1,600 sq.ft',
                'covered_area' => '1,600 sq.ft',
                'municipality' => 'Lalitpur',
                'district' => 'Lalitpur',
                'province' => 'Bagmati',
                'tole' => 'Kupondole',
                'photos' => [
                    'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
        ];

        foreach ($propertiesData as $data) {
            $existingProperty = Property::where('property_code', $data['property_code'])->first();

            if ($existingProperty) {
                // Update photos if existing
                PropertyPhoto::where('property_id', $existingProperty->property_id)->delete();
                foreach ($data['photos'] as $idx => $photoUrl) {
                    PropertyPhoto::create([
                        'property_id' => $existingProperty->property_id,
                        'source_type' => 'listing',
                        'photo_type' => $idx === 0 ? 'front' : 'interior',
                        'file_ref' => $photoUrl,
                        'caption' => "{$data['property_code']} Photo " . ($idx + 1),
                        'uploaded_at' => now(),
                    ]);
                }
                continue;
            }

            // 1. Create Address
            $address = Address::create([
                'province' => $data['province'],
                'district' => $data['district'],
                'municipality' => $data['municipality'],
                'ward_no' => '04',
                'tole_locality' => $data['tole'],
                'full_address_text' => "{$data['tole']}, {$data['municipality']}, {$data['district']}, {$data['province']}",
                'gps_verified' => false,
            ]);

            // 2. Create Property
            $property = Property::create([
                'property_code' => $data['property_code'],
                'owner_client_id' => $client->client_id,
                'ownership_role' => 'self',
                'property_type' => $data['property_type'],
                'address_id' => $address->address_id,
                'area' => $data['area'],
                'covered_area' => $data['covered_area'],
                'no_of_floors' => $data['no_of_floors'],
                'status' => 'listed',
                'approval_status' => 'approved',
            ]);

            // 3. Create Property Photos (all photos in array)
            foreach ($data['photos'] as $idx => $photoUrl) {
                PropertyPhoto::create([
                    'property_id' => $property->property_id,
                    'source_type' => 'listing',
                    'photo_type' => $idx === 0 ? 'front' : 'interior',
                    'file_ref' => $photoUrl,
                    'caption' => "{$data['property_code']} Photo " . ($idx + 1),
                    'uploaded_at' => now(),
                ]);
            }

            // 4. Create Property Listing
            PropertyListing::create([
                'application_no' => $data['application_no'],
                'property_id' => $property->property_id,
                'applicant_client_id' => $client->client_id,
                'purpose_of_listing' => $data['purpose'],
                'expected_selling_price' => $data['purpose'] === 'sale' ? $data['price'] : null,
                'rental_amount' => $data['purpose'] === 'rent' ? $data['price'] : null,
                'negotiable' => $data['negotiable'],
                'listing_status' => 'approved',
                'effective_date' => now(),
                'date_received' => now(),
            ]);
        }
    }
}

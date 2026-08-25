<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyListing;
use Illuminate\Database\Seeder;

class FeaturedListingsSeeder extends Seeder
{
    public function run(): void
    {
        if (PropertyListing::where('application_no', 'like', 'MLS-SEED-%')->count() >= 10) {
            return;
        }

        $client = Client::firstOrCreate(
            ['client_code' => 'SEED-CLIENT-001'],
            [
                'client_type' => 'owner',
                'full_name' => 'Seed Property Owner',
                'mobile_no' => '9800000001',
            ]
        );

        $listings = [
            [
                'application_no' => 'MLS-SEED-001',
                'property_code' => 'PROP-SEED-001',
                'purpose' => 'rent',
                'price' => 23000,
                'property_type' => 'apartment',
                'no_of_floors' => 1,
                'covered_area' => '650 sqft',
                'area' => '650 sqft',
                'province' => 'Bagmati Province',
                'district' => 'Kathmandu',
                'municipality' => 'Kathmandu Metropolitan City',
                'tole' => 'Baluwatar, Ward 4',
            ],
            [
                'application_no' => 'MLS-SEED-002',
                'property_code' => 'PROP-SEED-002',
                'purpose' => 'sale',
                'price' => 33500000,
                'property_type' => 'house',
                'no_of_floors' => 3,
                'covered_area' => '2,448 sqft',
                'area' => '5 aana',
                'province' => 'Gandaki Province',
                'district' => 'Kaski',
                'municipality' => 'Pokhara Metropolitan City',
                'tole' => 'Lakeside, Ward 6',
            ],
            [
                'application_no' => 'MLS-SEED-003',
                'property_code' => 'PROP-SEED-003',
                'purpose' => 'sale',
                'price' => 57500000,
                'property_type' => 'house',
                'no_of_floors' => 5,
                'covered_area' => '2,758 sqft',
                'area' => '8 aana',
                'province' => 'Bagmati Province',
                'district' => 'Lalitpur',
                'municipality' => 'Lalitpur Metropolitan City',
                'tole' => 'Pulchowk, Ward 3',
            ],
            [
                'application_no' => 'MLS-SEED-004',
                'property_code' => 'PROP-SEED-004',
                'purpose' => 'sale',
                'price' => 359900000,
                'property_type' => 'house',
                'no_of_floors' => 4,
                'covered_area' => '3,232 sqft',
                'area' => '1 ropani',
                'province' => 'Bagmati Province',
                'district' => 'Bhaktapur',
                'municipality' => 'Madhyapur Thimi Municipality',
                'tole' => 'Sallaghari, Ward 2',
            ],
            [
                'application_no' => 'MLS-SEED-005',
                'property_code' => 'PROP-SEED-005',
                'purpose' => 'sale',
                'price' => 12500000,
                'property_type' => 'land',
                'no_of_floors' => null,
                'covered_area' => null,
                'area' => '4 aana',
                'province' => 'Bagmati Province',
                'district' => 'Kathmandu',
                'municipality' => 'Kathmandu Metropolitan City',
                'tole' => 'Boudha, Ward 6',
            ],
            [
                'application_no' => 'MLS-SEED-006',
                'property_code' => 'PROP-SEED-006',
                'purpose' => 'rent',
                'price' => 45000,
                'property_type' => 'apartment',
                'no_of_floors' => 2,
                'covered_area' => '980 sqft',
                'area' => '980 sqft',
                'province' => 'Gandaki Province',
                'district' => 'Kaski',
                'municipality' => 'Pokhara Metropolitan City',
                'tole' => 'Mahendrapool, Ward 8',
            ],
            [
                'application_no' => 'MLS-SEED-007',
                'property_code' => 'PROP-SEED-007',
                'purpose' => 'sale',
                'price' => 8900000,
                'property_type' => 'commercial_building',
                'no_of_floors' => 3,
                'covered_area' => '1,800 sqft',
                'area' => '6 aana',
                'province' => 'Province 1',
                'district' => 'Morang',
                'municipality' => 'Biratnagar Metropolitan City',
                'tole' => 'Main Road, Ward 5',
            ],
            [
                'application_no' => 'MLS-SEED-008',
                'property_code' => 'PROP-SEED-008',
                'purpose' => 'sale',
                'price' => 42000000,
                'property_type' => 'house',
                'no_of_floors' => 2,
                'covered_area' => '1,950 sqft',
                'area' => '7 aana',
                'province' => 'Bagmati Province',
                'district' => 'Chitwan',
                'municipality' => 'Bharatpur Metropolitan City',
                'tole' => 'Narayangadh, Ward 10',
            ],
            [
                'application_no' => 'MLS-SEED-009',
                'property_code' => 'PROP-SEED-009',
                'purpose' => 'lease',
                'price' => 75000,
                'property_type' => 'office_space',
                'no_of_floors' => 1,
                'covered_area' => '1,200 sqft',
                'area' => '1,200 sqft',
                'province' => 'Bagmati Province',
                'district' => 'Kathmandu',
                'municipality' => 'Kathmandu Metropolitan City',
                'tole' => 'New Baneshwor, Ward 10',
            ],
            [
                'application_no' => 'MLS-SEED-010',
                'property_code' => 'PROP-SEED-010',
                'purpose' => 'sale',
                'price' => 18500000,
                'property_type' => 'house',
                'no_of_floors' => 3,
                'covered_area' => '2,100 sqft',
                'area' => '5.5 aana',
                'province' => 'Lumbini Province',
                'district' => 'Rupandehi',
                'municipality' => 'Butwal Sub-Metropolitan City',
                'tole' => 'Devinagar, Ward 7',
            ],
        ];

        foreach ($listings as $item) {
            if (PropertyListing::where('application_no', $item['application_no'])->exists()) {
                continue;
            }

            $address = Address::create([
                'province' => $item['province'],
                'district' => $item['district'],
                'municipality' => $item['municipality'],
                'tole_locality' => $item['tole'],
                'full_address_text' => $item['tole'] . ', ' . $item['municipality'] . ', ' . $item['district'],
            ]);

            $property = Property::create([
                'property_code' => $item['property_code'],
                'owner_client_id' => $client->client_id,
                'property_type' => $item['property_type'],
                'address_id' => $address->address_id,
                'no_of_floors' => $item['no_of_floors'],
                'covered_area' => $item['covered_area'],
                'area' => $item['area'],
                'status' => 'listed',
                'approval_status' => 'approved',
            ]);

            PropertyListing::create([
                'application_no' => $item['application_no'],
                'property_id' => $property->property_id,
                'applicant_client_id' => $client->client_id,
                'purpose_of_listing' => $item['purpose'],
                'expected_selling_price' => in_array($item['purpose'], ['sale', 'exchange', 'investment'], true)
                    ? $item['price']
                    : null,
                'rental_amount' => $item['purpose'] === 'rent' || $item['purpose'] === 'lease'
                    ? $item['price']
                    : null,
                'negotiable' => false,
                'legal_verification_status' => 'completed',
                'listing_status' => 'approved',
                'photographs_received' => true,
            ]);
        }
    }
}

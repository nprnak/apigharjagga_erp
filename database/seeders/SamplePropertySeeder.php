<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Client;
use App\Models\Property;
use App\Models\PropertyListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SamplePropertySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::create([
            'name' => 'Demo User',
            'email' => 'user@apigharjagga.com',
            'password' => bcrypt('password'),
        ]);

        $samples = [
            [
                'property_type' => 'house',
                'purpose' => 'sale',
                'price' => 35000000,
                'rental' => null,
                'area' => '0-5-2-1 (5.5 Aana)',
                'covered_area' => '2800 sq.ft',
                'no_of_floors' => 3,
                'facing_direction' => 'East',
                'province' => 'Bagmati',
                'district' => 'Kathmandu',
                'municipality' => 'Kathmandu',
                'ward_no' => '4',
                'tole' => 'Baluwatar, VIP Residential Area',
                'photo_seed' => 'baluwatar-modern-house',
            ],
            [
                'property_type' => 'apartment',
                'purpose' => 'sale',
                'price' => 22500000,
                'rental' => null,
                'area' => '1650 sq.ft',
                'covered_area' => '1650 sq.ft',
                'no_of_floors' => 8,
                'facing_direction' => 'South',
                'province' => 'Bagmati',
                'district' => 'Lalitpur',
                'municipality' => 'Lalitpur',
                'ward_no' => '3',
                'tole' => 'Jhamsikhel, Sanepa Height',
                'photo_seed' => 'luxury-apartment-jhamsikhel',
            ],
            [
                'property_type' => 'land',
                'purpose' => 'sale',
                'price' => 18000000,
                'rental' => null,
                'area' => '0-8-0-0 (8 Aana)',
                'covered_area' => null,
                'no_of_floors' => null,
                'facing_direction' => 'North-East',
                'province' => 'Gandaki',
                'district' => 'Kaski',
                'municipality' => 'Pokhara',
                'ward_no' => '6',
                'tole' => 'Lakeside, View of Fewa Lake',
                'photo_seed' => 'pokhara-lakeside-land',
            ],
            [
                'property_type' => 'commercial_building',
                'purpose' => 'rent',
                'price' => null,
                'rental' => 175000,
                'area' => '0-6-0-0 (6 Aana)',
                'covered_area' => '4200 sq.ft',
                'no_of_floors' => 5,
                'facing_direction' => 'West',
                'province' => 'Bagmati',
                'district' => 'Kathmandu',
                'municipality' => 'Kathmandu',
                'ward_no' => '10',
                'tole' => 'New Baneshwor, Main Road Highway Access',
                'photo_seed' => 'baneshwor-commercial-complex',
            ],
            [
                'property_type' => 'house',
                'purpose' => 'sale',
                'price' => 28000000,
                'rental' => null,
                'area' => '0-4-1-0 (4.25 Aana)',
                'covered_area' => '2200 sq.ft',
                'no_of_floors' => 2,
                'facing_direction' => 'South-East',
                'province' => 'Bagmati',
                'district' => 'Bhaktapur',
                'municipality' => 'Bhaktapur',
                'ward_no' => '2',
                'tole' => 'Sallaghari, Peace Enclave Colony',
                'photo_seed' => 'bhaktapur-bungalow',
            ],
            [
                'property_type' => 'agricultural_land',
                'purpose' => 'sale',
                'price' => 12000000,
                'rental' => null,
                'area' => '2-4-0-0 (2 Ropani 4 Aana)',
                'covered_area' => null,
                'no_of_floors' => null,
                'facing_direction' => 'East',
                'province' => 'Bagmati',
                'district' => 'Chitwan',
                'municipality' => 'Bharatpur',
                'ward_no' => '12',
                'tole' => 'Baseni, Fertile Agro Land',
                'photo_seed' => 'bharatpur-chitwan-farm',
            ],
        ];

        foreach ($samples as $index => $item) {
            $address = Address::create([
                'province' => $item['province'],
                'district' => $item['district'],
                'municipality' => $item['municipality'],
                'ward_no' => $item['ward_no'],
                'tole_locality' => $item['tole'],
            ]);

            $client = Client::firstOrCreate(
                ['mobile_app_user_id' => (string) $user->id],
                [
                    'client_code' => 'CLT-U' . $user->id,
                    'client_type' => 'owner',
                    'full_name' => $user->name,
                    'email' => $user->email,
                    'mobile_no' => '9800000000',
                    'current_address_id' => $address->address_id,
                    'registration_date' => now()->toDateString(),
                    'mis_entry_status' => 'completed',
                    'is_active' => true,
                ]
            );

            $propCode = 'PROP-' . strtoupper(Str::random(6)) . ($index + 1);

            $property = Property::create([
                'property_code' => $propCode,
                'owner_client_id' => $client->client_id,
                'user_id' => $user->id,
                'ownership_role' => 'self',
                'property_type' => $item['property_type'],
                'address_id' => $address->address_id,
                'kitta_no' => (string) rand(100, 9999),
                'area' => $item['area'],
                'covered_area' => $item['covered_area'],
                'no_of_floors' => $item['no_of_floors'],
                'year_of_construction' => 2022,
                'facing_direction' => $item['facing_direction'],
                'structure_type' => 'RCC Frame Earthquake Resistant',
                'parking' => '2 Cars + 4 Bikes',
                'status' => 'listed',
                'approval_status' => 'approved',
            ]);

            PropertyListing::create([
                'application_no' => 'AGJ-' . date('Ymd') . '-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'property_id' => $property->property_id,
                'applicant_client_id' => $client->client_id,
                'purpose_of_listing' => $item['purpose'],
                'expected_selling_price' => $item['price'],
                'rental_amount' => $item['rental'],
                'photographs_received' => true,
                'listing_status' => 'approved',
            ]);
        }
    }
}

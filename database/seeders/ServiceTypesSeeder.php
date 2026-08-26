<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            'Property Listing',
            'Verification',
            'Valuation',
            'Digital Marketing',
            'Consultation',
            'Documentation Support',
        ];

        foreach ($serviceTypes as $serviceName) {
            DB::table('service_types')->updateOrInsert(
                ['service_name' => $serviceName],
                ['service_name' => $serviceName, 'is_active' => true],
            );
        }
    }
}

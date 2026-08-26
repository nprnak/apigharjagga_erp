<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertyFeatureTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            'Corner Plot',
            'Blacktopped Road',
            'Drinking Water',
            'School Nearby',
            'Hospital Nearby',
            'Electricity',
            'Sewerage',
            'Parking Space',
            'Gated Community',
            'Market Nearby',
        ];

        foreach ($features as $featureName) {
            DB::table('property_feature_types')->updateOrInsert(['feature_name' => $featureName]);
        }
    }
}

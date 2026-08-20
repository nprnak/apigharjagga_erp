<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            ['doc_name' => 'Citizenship Copy', 'category' => 'identity'],
            ['doc_name' => 'Lalpurja', 'category' => 'land'],
            ['doc_name' => 'Tax Clearance', 'category' => 'financial'],
            ['doc_name' => 'Blueprint', 'category' => 'building'],
            ['doc_name' => 'Building Completion Certificate', 'category' => 'building'],
            ['doc_name' => 'Valuation Report', 'category' => 'financial'],
            ['doc_name' => 'Power of Attorney', 'category' => 'identity'],
            ['doc_name' => 'Utility Bills', 'category' => 'financial'],
            ['doc_name' => 'Photographs', 'category' => 'land'],
        ];

        foreach ($documentTypes as $documentType) {
            DB::table('document_types')->updateOrInsert(
                ['doc_name' => $documentType['doc_name']],
                $documentType,
            );
        }
    }
}

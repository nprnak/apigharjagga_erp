<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('valuation_reports', function (Blueprint $table): void {
            $table->decimal('land_area', 14, 4)->default(0)->after('valuation_type');
            $table->decimal('land_rate', 14, 2)->default(0)->after('land_area');
            $table->decimal('building_area', 14, 4)->default(0)->after('land_rate');
            $table->decimal('building_rate', 14, 2)->default(0)->after('building_area');
            $table->decimal('depreciation_percent', 5, 2)->default(0)->after('building_rate');
            $table->decimal('adjustment_amount', 14, 2)->default(0)->after('depreciation_percent');
        });
    }

    public function down(): void
    {
        Schema::table('valuation_reports', function (Blueprint $table): void {
            $table->dropColumn([
                'land_area',
                'land_rate',
                'building_area',
                'building_rate',
                'depreciation_percent',
                'adjustment_amount',
            ]);
        });
    }
};

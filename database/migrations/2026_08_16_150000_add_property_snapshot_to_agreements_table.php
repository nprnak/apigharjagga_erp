<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: agreements (additive)
     * Purpose: Capture the property description exactly as written on Annex B (PO-PB) —
     *          house description and the four boundaries (चार किल्ला) — plus the purchase
     *          price spelled out in words. These are agreement-time snapshots and may
     *          differ from the live `properties` record, so they live on the agreement
     *          itself rather than being merged into `properties`.
     */
    public function up(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->text('house_description')->nullable()->after('property_id');
            $table->string('boundary_east', 150)->nullable()->after('house_description');
            $table->string('boundary_west', 150)->nullable()->after('boundary_east');
            $table->string('boundary_north', 150)->nullable()->after('boundary_west');
            $table->string('boundary_south', 150)->nullable()->after('boundary_north');
            $table->string('total_price_words', 255)->nullable()->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn([
                'house_description',
                'boundary_east',
                'boundary_west',
                'boundary_north',
                'boundary_south',
                'total_price_words',
            ]);
        });
    }
};

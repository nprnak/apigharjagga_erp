<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table: property_photos
     * Purpose: Reusable photo store for a property.
     *          A photo can originate from listing, inspection, verification, or handover.
     *          The source is tracked via source_type/source_id (polymorphic link kept
     *          explicit for indexability; FK to source table enforced at application layer).
     * Dependency order: #16 — references properties(property_id) [CASCADE].
     */
    public function up(): void
    {
        Schema::create('property_photos', function (Blueprint $table) {
            // BIGSERIAL PRIMARY KEY → bigIncrements
            $table->bigIncrements('photo_id');

            $table->unsignedBigInteger('property_id');

            // CHECK (source_type IN (...)) → enum; NOT NULL
            $table->enum('source_type', [
                'listing',
                'site_inspection',
                'verification',
                'handover',
                'other',
            ]);

            // FK to the source record — enforced in application layer, not DB
            $table->unsignedBigInteger('source_id')->nullable()
                  ->comment('FK to source record (listing/inspection/verification/handover); enforced at application layer');

            // CHECK (photo_type IN (...)) → enum; nullable
            $table->enum('photo_type', [
                'front',
                'rear',
                'side',
                'road_access',
                'boundary',
                'interior',
                'surrounding',
                'other',
            ])->nullable();

            $table->text('file_ref');
            $table->string('caption', 200)->nullable();

            // TIMESTAMPTZ NOT NULL DEFAULT now()
            // MySQL TIMESTAMP has no timezone-awareness; stored/retrieved in server timezone.
            $table->timestamp('uploaded_at')->useCurrent();

            // ON DELETE CASCADE — explicitly specified in schema.sql
            $table->foreign('property_id')
                  ->references('property_id')
                  ->on('properties')
                  ->cascadeOnDelete();

            // Index from original schema
            $table->index('property_id', 'idx_property_photos_property');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_photos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds the fields needed to actually run the Annex-D Site Inspection
     * Checklist workflow end-to-end from the admin panel:
     *  - who performed / reviewed the inspection (real `users`, not the
     *    legacy `staff` lookup table the original `site_inspections`
     *    columns reference)
     *  - the general-info fields captured at inspection time (§1 of the form)
     *  - the fixed-length checklists (§2, §3, §5, §6) as JSON, since each is
     *    a small, fixed set of items rather than an open-ended list
     *  - the submit/review workflow (draft → submitted → reviewed)
     */
    public function up(): void
    {
        Schema::table('site_inspections', function (Blueprint $table) {
            $table->foreignId('inspector_user_id')->nullable()->after('inspector_staff_id')
                ->constrained('users')->nullOnDelete();

            $table->string('property_owner_name', 150)->nullable()->after('property_id');
            $table->string('contact_number', 30)->nullable()->after('property_owner_name');
            $table->string('property_location', 255)->nullable()->after('contact_number');
            $table->string('municipality', 100)->nullable()->after('property_location');
            $table->string('ward_no', 20)->nullable()->after('municipality');
            $table->string('inspector_designation', 100)->nullable()->after('inspector_user_id');

            // §2 (9 land items) / §3 (10 building items): { key => { verified: bool|null, remarks: string|null } }
            $table->json('land_checklist')->nullable()->after('observation_notes');
            $table->json('building_checklist')->nullable()->after('land_checklist');
            // §5 (7 photos) / §6 (6 documents): { key => bool }
            $table->json('photo_checklist')->nullable()->after('building_checklist');
            $table->json('documents_checklist')->nullable()->after('photo_checklist');

            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft')->after('final_status');
            $table->enum('submitted_to', ['valuation_officer', 'admin'])->nullable()->after('status');
            $table->timestamp('submitted_at')->nullable()->after('submitted_to');

            $table->foreignId('reviewed_by_user_id')->nullable()->after('verified_by_staff_id')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by_user_id');
            $table->text('review_notes')->nullable()->after('reviewed_at');

            // The original table only had `created_at` (no `updated_at`).
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('site_inspections', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inspector_user_id');
            $table->dropConstrainedForeignId('reviewed_by_user_id');

            $table->dropColumn([
                'property_owner_name',
                'contact_number',
                'property_location',
                'municipality',
                'ward_no',
                'inspector_designation',
                'land_checklist',
                'building_checklist',
                'photo_checklist',
                'documents_checklist',
                'status',
                'submitted_to',
                'submitted_at',
                'reviewed_at',
                'review_notes',
                'updated_at',
            ]);
        });
    }
};

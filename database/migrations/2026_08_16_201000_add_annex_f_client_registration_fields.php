<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('client_type_other', 100)->nullable()->after('client_type');
            $table->string('signature_name', 150)->nullable()->after('is_active');
            $table->string('signature_path', 255)->nullable()->after('signature_name');
            $table->date('signature_date')->nullable()->after('signature_path');
            $table->string('registered_by_name', 150)->nullable()->after('signature_date');
            $table->string('registered_by_designation', 100)->nullable()->after('registered_by_name');
            $table->string('registered_by_signature_path', 255)->nullable()->after('registered_by_designation');
            $table->date('registered_by_date')->nullable()->after('registered_by_signature_path');
            $table->string('approved_by_name', 150)->nullable()->after('registered_by_date');
            $table->string('approved_by_designation', 100)->nullable()->after('approved_by_name');
            $table->string('approved_by_signature_path', 255)->nullable()->after('approved_by_designation');
            $table->date('approved_by_date')->nullable()->after('approved_by_signature_path');
        });

        Schema::create('client_owner_listings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->json('available_for')->nullable();
            $table->string('property_location', 200)->nullable();
            $table->string('kitta_no', 50)->nullable();
            $table->string('land_area', 100)->nullable();
            $table->text('building_details')->nullable();
            $table->decimal('expected_price', 14, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('client_id')
                ->references('client_id')
                ->on('clients')
                ->cascadeOnDelete();
        });

        $services = [
            'Property Listing Service',
            'Property Verification Service',
            'Property Valuation Service',
            'Digital Marketing Service',
            'Property Consultation',
            'Documentation Support',
        ];
        foreach ($services as $name) {
            DB::table('service_types')->insertOrIgnore([
                'service_name' => $name,
                'is_active'    => 1,
            ]);
        }

        $documents = [
            ['Citizenship Copy', 'identity'],
            ['Ownership Certificate Copy', 'land'],
            ['Land/House Documents', 'land'],
            ['Passport Size Photo', 'identity'],
            ['Authorization Letter', 'identity'],
            ['Other Documents', 'other'],
        ];
        foreach ($documents as [$name, $category]) {
            DB::table('document_types')->insertOrIgnore([
                'doc_name' => $name,
                'category' => $category,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('client_owner_listings');

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'client_type_other',
                'signature_name',
                'signature_path',
                'signature_date',
                'registered_by_name',
                'registered_by_designation',
                'registered_by_signature_path',
                'registered_by_date',
                'approved_by_name',
                'approved_by_designation',
                'approved_by_signature_path',
                'approved_by_date',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('received_through_other', 100)->nullable()->after('received_through');
            $table->string('category_other', 150)->nullable()->after('category');
            $table->string('property_location', 200)->nullable()->after('property_id');
            $table->string('kitta_no', 50)->nullable()->after('property_location');
            $table->string('assigned_officer_name', 150)->nullable()->after('assigned_department');

            $table->string('received_by_name', 150)->nullable()->after('received_by_staff_id');
            $table->string('received_by_designation', 100)->nullable()->after('received_by_name');
            $table->string('received_by_signature_path', 255)->nullable()->after('received_by_designation');
            $table->date('received_by_date')->nullable()->after('received_by_signature_path');

            $table->string('customer_signature_name', 150)->nullable()->after('customer_remarks');
            $table->string('customer_signature_path', 255)->nullable()->after('customer_signature_name');
            $table->date('customer_signature_date')->nullable()->after('customer_signature_path');

            $table->string('reviewed_by_name', 150)->nullable()->after('customer_signature_date');
            $table->string('reviewed_by_designation', 100)->nullable()->after('reviewed_by_name');
            $table->string('reviewed_by_signature_path', 255)->nullable()->after('reviewed_by_designation');
            $table->date('reviewed_by_date')->nullable()->after('reviewed_by_signature_path');
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropColumn([
                'received_through_other',
                'category_other',
                'property_location',
                'kitta_no',
                'assigned_officer_name',
                'received_by_name',
                'received_by_designation',
                'received_by_signature_path',
                'received_by_date',
                'customer_signature_name',
                'customer_signature_path',
                'customer_signature_date',
                'reviewed_by_name',
                'reviewed_by_designation',
                'reviewed_by_signature_path',
                'reviewed_by_date',
            ]);
        });
    }
};

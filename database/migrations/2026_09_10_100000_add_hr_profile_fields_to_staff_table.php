<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('employee_code', 30)->nullable()->unique()->after('staff_id');
            $table->string('department', 100)->nullable()->after('designation');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'probation'])->default('full_time')->after('department');
            $table->date('date_of_joining')->nullable()->after('employment_type');
            $table->decimal('basic_salary', 12, 2)->nullable()->after('date_of_joining');
            $table->date('date_of_birth')->nullable()->after('basic_salary');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->string('address', 255)->nullable()->after('gender');
            $table->string('emergency_contact_name', 150)->nullable()->after('address');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'employee_code', 'department', 'employment_type', 'date_of_joining',
                'basic_salary', 'date_of_birth', 'gender', 'address',
                'emergency_contact_name', 'emergency_contact_phone',
            ]);
        });
    }
};

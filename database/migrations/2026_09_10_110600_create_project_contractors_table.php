<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A contractor's assignment to one project, with the per-project terms
     * (scope, contract amount) that don't belong on the Contractor
     * directory record itself.
     */
    public function up(): void
    {
        Schema::create('project_contractors', function (Blueprint $table) {
            $table->id('project_contractor_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('contractor_id');
            $table->string('work_scope', 255)->nullable();
            $table->decimal('contract_amount', 14, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'terminated'])->default('active');

            $table->foreign('project_id')->references('project_id')->on('projects')->cascadeOnDelete();
            $table->foreign('contractor_id')->references('contractor_id')->on('contractors');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_contractors');
    }
};

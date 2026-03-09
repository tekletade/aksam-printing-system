<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();

            $table->integer('max_days_per_year')->nullable();
            $table->integer('max_consecutive_days')->nullable();
            $table->integer('minimum_notice_days')->default(1);

            $table->boolean('is_paid')->default(true);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('requires_document')->default(false);
            $table->boolean('carry_forward')->default(false);
            $table->integer('carry_forward_limit')->nullable();
            $table->boolean('is_active')->default(true);

            $table->json('applicable_gender')->nullable(); // ['male', 'female']
            $table->json('applicable_employment_types')->nullable(); // ['permanent', 'contract']

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};

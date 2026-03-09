<?php
// database/migrations/[timestamp]_create_tax_brackets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_brackets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['income_tax', 'pension_employee', 'pension_employer', 'vat', 'withholding']);
            $table->decimal('rate', 5, 2);

            // For progressive tax (income tax)
            $table->decimal('min_amount', 12, 2)->nullable();
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->decimal('fixed_amount', 12, 2)->nullable();
            $table->decimal('excess_rate', 5, 2)->nullable();

            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['type', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_brackets');
    }
};

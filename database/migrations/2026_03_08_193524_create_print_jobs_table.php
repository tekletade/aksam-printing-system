<?php
// database/migrations/[timestamp]_create_print_jobs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->constrained();

            // Make order_id nullable and add index first, then add foreign key separately
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained(); // employee who processed

            $table->string('job_id')->nullable(); // printer's job ID
            $table->string('document_name')->nullable();
            $table->string('owner')->nullable(); // username who printed

            // Job details
            $table->integer('pages')->default(0);
            $table->integer('copies')->default(1);
            $table->integer('total_sheets')->default(0);
            $table->enum('color_mode', ['black_white', 'color', 'grayscale'])->default('black_white');
            $table->enum('print_side', ['simplex', 'duplex'])->default('simplex');
            $table->string('paper_size')->nullable();
            $table->string('file_size')->nullable();

            // For plotters
            $table->decimal('length_meters', 8, 2)->nullable();

            // Timing
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_seconds')->nullable();

            // Revenue
            $table->decimal('price_per_page', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('currency')->default('ETB');

            $table->enum('status', [
                'pending', 'processing', 'completed',
                'cancelled', 'error', 'held'
            ])->default('pending');

            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Add indexes first
            $table->index(['printer_id', 'created_at']);
            $table->index('status');
            $table->index('completed_at');
            $table->index('order_id');
        });

        // Now add the foreign key constraint separately
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};

<?php
// database/migrations/[timestamp]_create_paper_inventory_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paper_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->constrained()->onDelete('cascade');
            $table->string('tray_name');
            $table->string('paper_size'); // A4, A3, Letter, etc.
            $table->string('paper_type'); // Plain, Glossy, Recycled, etc.
            $table->integer('current_sheets');
            $table->integer('max_capacity');
            $table->integer('threshold_reorder')->default(100);
            $table->integer('threshold_critical')->default(50);
            $table->boolean('is_low')->default(false);
            $table->boolean('is_empty')->default(false);
            $table->timestamp('last_refilled_at')->nullable();
            $table->timestamps();

            $table->index(['printer_id', 'paper_size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paper_inventory');
    }
};

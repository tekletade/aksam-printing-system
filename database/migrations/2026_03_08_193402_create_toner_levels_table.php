<?php
// database/migrations/[timestamp]_create_toner_levels_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toner_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->constrained()->onDelete('cascade');
            $table->string('toner_color')->default('black'); // black, cyan, magenta, yellow
            $table->string('toner_model')->nullable();
            $table->string('toner_serial')->nullable();
            $table->integer('current_level'); // percentage 0-100
            $table->integer('estimated_pages_remaining')->nullable();
            $table->integer('threshold_warning')->default(15);
            $table->integer('threshold_critical')->default(5);
            $table->boolean('is_low')->default(false);
            $table->boolean('is_critical')->default(false);
            $table->timestamp('last_replaced_at')->nullable();
            $table->timestamps();

            $table->index(['printer_id', 'toner_color', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toner_levels');
    }
};

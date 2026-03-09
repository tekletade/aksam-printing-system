<?php
// database/migrations/[timestamp]_create_printer_models_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printer_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('brand', ['Konica', 'Canon', 'KIP', 'HP']);
            $table->string('model_number');
            $table->json('specifications')->nullable();
            $table->json('supported_media_types')->nullable();
            $table->integer('default_paper_capacity')->nullable();
            $table->boolean('is_color')->default(true);
            $table->boolean('is_duplex_supported')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['brand', 'model_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printer_models');
    }
};

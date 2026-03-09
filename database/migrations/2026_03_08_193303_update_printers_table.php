<?php
// database/migrations/[timestamp]_update_printers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First drop if exists (from previous step)
        Schema::dropIfExists('printers');

        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_model_id')->nullable()->constrained();
            $table->string('name');
            $table->string('ip_address')->unique();
            $table->string('mac_address')->nullable();
            $table->string('serial_number')->unique();
            $table->enum('status', [
                'Ready', 'Printing', 'Error', 'Offline',
                'Maintenance', 'Paper Jam', 'Toner Low', 'Warming Up'
            ])->default('Ready');
            $table->string('location')->nullable();
            $table->string('department')->nullable();

            // Counter readings
            $table->bigInteger('total_pages_count')->default(0);
            $table->bigInteger('black_white_pages')->default(0);
            $table->bigInteger('color_pages')->default(0);
            $table->bigInteger('simplex_pages')->default(0);
            $table->bigInteger('duplex_pages')->default(0);

            // For plotters
            $table->decimal('total_print_length_meters', 10, 2)->default(0);

            // Configuration
            $table->json('configuration')->nullable();
            $table->json('capabilities')->nullable();

            // Maintenance
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->integer('maintenance_interval_days')->default(90);

            // SNMP Settings
            $table->string('snmp_community')->default('public');
            $table->integer('snmp_port')->default(161);
            $table->string('snmp_version')->default('v2c');

            $table->boolean('is_monitoring_enabled')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_polled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_active']);
            $table->index('department');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};

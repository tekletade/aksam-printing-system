<?php
// database/migrations/[timestamp]_create_printer_status_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printer_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->string('previous_status')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('logged_at');
            $table->timestamps();

            $table->index(['printer_id', 'logged_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printer_status_logs');
    }
};

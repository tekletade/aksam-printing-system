<?php
// database/migrations/[timestamp]_create_shifts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();

            $table->time('start_time');
            $table->time('end_time');
            $table->integer('break_minutes')->default(60);
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();

            $table->enum('type', ['day', 'night', 'rotating'])->default('day');

            $table->json('applicable_days')->nullable(); // ['monday', 'tuesday', ...]

            $table->decimal('hourly_rate_multiplier', 3, 2)->default(1.00);
            $table->decimal('overtime_multiplier', 3, 2)->default(1.50);
            $table->decimal('night_differential_multiplier', 3, 2)->default(1.25);

            $table->integer('grace_late_minutes')->default(15);
            $table->integer('grace_early_leave_minutes')->default(15);

            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};

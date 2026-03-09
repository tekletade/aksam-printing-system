<?php
// database/migrations/[timestamp]_create_attendance_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->constrained();

            $table->date('attendance_date');

            // Check in/out
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->string('check_in_method')->nullable(); // manual, biometric, mobile
            $table->string('check_out_method')->nullable();
            $table->string('check_in_location')->nullable();
            $table->string('check_out_location')->nullable();

            // Calculated fields
            $table->integer('total_minutes')->nullable();
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->decimal('regular_hours', 5, 2)->nullable();
            $table->decimal('overtime_hours', 5, 2)->nullable();
            $table->decimal('night_differential_hours', 5, 2)->nullable();

            // Status flags
            $table->boolean('is_late')->default(false);
            $table->integer('late_minutes')->nullable();
            $table->boolean('is_early_leave')->default(false);
            $table->integer('early_leave_minutes')->nullable();
            $table->boolean('is_overtime')->default(false);
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_holiday')->default(false);
            $table->boolean('is_weekend')->default(false);

            // Break
            $table->timestamp('break_start')->nullable();
            $table->timestamp('break_end')->nullable();
            $table->integer('break_minutes')->nullable();

            // Status
            $table->enum('status', [
                'present', 'absent', 'late', 'half_day',
                'on_leave', 'holiday', 'weekend', 'not_scheduled'
            ])->default('present');

            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'attendance_date']);
            $table->index(['attendance_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};

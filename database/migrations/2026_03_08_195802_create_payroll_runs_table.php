<?php
// database/migrations/[timestamp]_create_payroll_runs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('employee_salary_id')->nullable()->constrained();

            // Earnings
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('housing_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('position_allowance', 12, 2)->default(0);
            $table->decimal('other_allowances', 12, 2)->default(0);

            // Attendance based
            $table->decimal('regular_hours', 8, 2)->default(0);
            $table->decimal('regular_pay', 12, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('overtime_pay', 12, 2)->default(0);
            $table->decimal('night_differential_hours', 8, 2)->default(0);
            $table->decimal('night_differential_pay', 12, 2)->default(0);

            // Other earnings
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('commission', 12, 2)->default(0);
            $table->decimal('reimbursement', 12, 2)->default(0);

            $table->decimal('gross_pay', 12, 2)->default(0);

            // Deductions
            $table->decimal('income_tax', 12, 2)->default(0);
            $table->decimal('pension_employee', 12, 2)->default(0);
            $table->decimal('pension_employer', 12, 2)->default(0);
            $table->decimal('loan_deduction', 12, 2)->default(0);
            $table->decimal('advance_deduction', 12, 2)->default(0);
            $table->decimal('other_deductions', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);

            $table->decimal('net_pay', 12, 2)->default(0);

            // Attendance summary
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('holiday_days')->default(0);

            $table->enum('status', [
                'pending', 'calculated', 'reviewed',
                'approved', 'paid', 'cancelled'
            ])->default('pending');

            $table->text('notes')->nullable();
            $table->json('calculations')->nullable(); // Store calculation breakdown
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id']);
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};

<?php
// database/migrations/[timestamp]_create_payroll_periods_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['monthly', 'bi-weekly', 'weekly'])->default('monthly');

            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date');

            $table->enum('status', [
                'open', 'processing', 'review', 'approved',
                'paid', 'closed', 'cancelled'
            ])->default('open');

            $table->text('notes')->nullable();

            // Summary totals
            $table->decimal('total_gross_pay', 14, 2)->default(0);
            $table->decimal('total_deductions', 14, 2)->default(0);
            $table->decimal('total_net_pay', 14, 2)->default(0);
            $table->decimal('total_tax', 14, 2)->default(0);
            $table->decimal('total_pension_employee', 14, 2)->default(0);
            $table->decimal('total_pension_employer', 14, 2)->default(0);

            $table->integer('employee_count')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['type', 'start_date', 'end_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};

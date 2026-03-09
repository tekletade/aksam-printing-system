<?php
// database/migrations/[timestamp]_create_deductions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('payroll_run_id')->nullable()->constrained();

            $table->string('deduction_type'); // loan, advance, union, etc.
            $table->string('reference_number')->nullable();
            $table->text('description');

            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);

            $table->date('deduction_date');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('installments')->nullable();
            $table->integer('installments_paid')->default(0);

            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');

            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deductions');
    }
};

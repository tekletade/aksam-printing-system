<?php
// database/migrations/[timestamp]_create_employee_salaries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');

            $table->decimal('basic_salary', 12, 2);
            $table->decimal('housing_allowance', 12, 2)->default(0);
            $table->decimal('transport_allowance', 12, 2)->default(0);
            $table->decimal('position_allowance', 12, 2)->default(0);
            $table->decimal('other_allowances', 12, 2)->default(0);

            $table->decimal('gross_salary', 12, 2); // sum of all above

            $table->enum('pay_frequency', ['monthly', 'bi-weekly', 'weekly'])->default('monthly');

            // Bank details (can override employee default)
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_branch')->nullable();

            // Tax settings
            $table->boolean('is_tax_exempt')->default(false);
            $table->string('tax_exemption_reason')->nullable();

            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);

            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};

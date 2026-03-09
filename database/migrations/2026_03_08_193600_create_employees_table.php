<?php
// database/migrations/[timestamp]_create_employees_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('employee_id')->unique();
            $table->string('first_name');
            $table->string('father_name');
            $table->string('grandfather_name');
            $table->string('last_name')->nullable();

            // Personal Info
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('nationality')->default('Ethiopian');
            $table->string('marital_status')->nullable();

            // Contact
            $table->string('phone_primary');
            $table->string('phone_secondary')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();

            // Address
            $table->string('city')->nullable();
            $table->string('sub_city')->nullable();
            $table->string('woreda')->nullable();
            $table->string('kebele')->nullable();
            $table->string('house_number')->nullable();

            // Employment
            $table->string('department');
            $table->string('position');
            $table->string('job_title');
            $table->enum('employment_type', [
                'permanent', 'contract', 'probation', 'intern', 'temporary', 'consultant'
            ])->default('permanent');

            $table->date('hire_date');
            $table->date('probation_end_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->string('termination_reason')->nullable();

            // IDs
            $table->string('pension_number')->nullable(); // Ethiopian pension
            $table->string('tin_number')->nullable(); // Tax ID
            $table->string('passport_number')->nullable();
            $table->string('kebele_id_number')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive', 'on_leave', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();

            $table->foreignId('reports_to')->nullable()->constrained('employees');
            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['department', 'status']);
            $table->index('employee_id');
            $table->index('hire_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

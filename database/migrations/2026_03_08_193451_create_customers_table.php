<?php
// database/migrations/[timestamp]_create_customers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code')->unique();
            $table->enum('type', ['individual', 'company', 'vip', 'walk_in'])->default('individual');
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('tin_number')->nullable(); // Tax Identification Number
            $table->string('vat_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('alternate_phone')->nullable();

            // Contact person for companies
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone')->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('sub_city')->nullable();
            $table->string('woreda')->nullable();
            $table->string('house_number')->nullable();

            // Communication channels
            $table->string('telegram_chat_id')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('preferred_channel')->default('telegram');

            // Account info
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->decimal('outstanding_balance', 12, 2)->default(0);
            $table->decimal('total_purchases', 12, 2)->default(0);
            $table->integer('total_orders')->default(0);

            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->text('notes')->nullable();
            $table->json('preferences')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index('phone');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 20)->unique();  // This is the column that's missing
            $table->string('name');
            $table->string('alternative_name')->nullable();

            $table->enum('type', [
                'asset', 'liability', 'equity',
                'income', 'expense', 'contra_asset', 'contra_liability'
            ]);

            $table->enum('category', [
                'current_asset', 'fixed_asset', 'current_liability',
                'long_term_liability', 'revenue', 'cogs',
                'operating_expense', 'other_income', 'other_expense'
            ]);

            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts');
            $table->integer('level')->default(1);

            $table->boolean('is_control_account')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_bank_account')->default(false);
            $table->boolean('is_cash_account')->default(false);
            $table->boolean('is_tax_account')->default(false);

            $table->string('currency')->default('ETB');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);

            $table->text('description')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index('account_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};

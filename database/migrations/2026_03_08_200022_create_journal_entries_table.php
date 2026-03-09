<?php
// database/migrations/[timestamp]_create_journal_entries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number')->unique();
            $table->foreignId('account_id')->constrained('chart_of_accounts');

            $table->date('entry_date');
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 15, 2);

            $table->string('reference_type')->nullable(); // payroll, order, payment
            $table->string('reference_id')->nullable();
            $table->string('reference_number')->nullable();

            $table->text('description');
            $table->text('notes')->nullable();

            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');

            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users');

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['account_id', 'entry_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('journal_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};

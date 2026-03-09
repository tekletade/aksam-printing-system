<?php
// database/migrations/[timestamp]_create_inventory_transactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->enum('transaction_type', ['purchase', 'usage', 'adjustment', 'return', 'transfer']);

            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('total_amount', 15, 2)->nullable();

            $table->decimal('stock_before', 12, 2);
            $table->decimal('stock_after', 12, 2);

            $table->string('reference_type')->nullable(); // order, purchase, adjustment
            $table->string('reference_id')->nullable();
            $table->string('reference_number')->nullable();

            $table->foreignId('printer_id')->nullable()->constrained();
            $table->foreignId('user_id')->nullable()->constrained(); // who performed
            $table->foreignId('supplier_id')->nullable();

            $table->text('reason')->nullable();
            $table->text('notes')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['inventory_item_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};

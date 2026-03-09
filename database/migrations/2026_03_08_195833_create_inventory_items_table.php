<?php
// database/migrations/[timestamp]_create_inventory_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('name');
            $table->enum('category', ['toner', 'paper', 'maintenance', 'other']);

            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('compatible_with')->nullable(); // printer models

            $table->string('unit_of_measure')->default('piece');
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('minimum_stock', 12, 2)->default(0);
            $table->decimal('maximum_stock', 12, 2)->nullable();
            $table->decimal('reorder_point', 12, 2)->default(0);
            $table->decimal('reorder_quantity', 12, 2)->nullable();

            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->nullable();

            $table->string('location')->nullable();
            $table->string('supplier')->nullable();
            $table->string('supplier_contact')->nullable();

            $table->date('last_ordered_at')->nullable();
            $table->date('last_received_at')->nullable();

            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock', 'discontinued'])
                  ->default('in_stock');

            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'status']);
            $table->index('item_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};

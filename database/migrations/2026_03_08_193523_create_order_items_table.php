<?php
// database/migrations/[timestamp]_create_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('printer_id')->nullable()->constrained();

            $table->string('item_type'); // print, copy, scan, bind, etc.
            $table->string('description');

            // Print specific
            $table->enum('color_mode', ['black_white', 'color', 'grayscale'])->nullable();
            $table->enum('print_side', ['simplex', 'duplex'])->nullable();
            $table->string('paper_size')->nullable();
            $table->string('paper_type')->nullable();
            $table->integer('pages')->nullable();
            $table->integer('copies')->default(1);
            $table->integer('total_pages')->nullable(); // pages * copies

            // For large format
            $table->decimal('length_meters', 8, 2)->nullable();
            $table->decimal('width_meters', 8, 2)->nullable();

            // Pricing
            $table->decimal('unit_price', 12, 2);
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])
                  ->default('pending');

            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

<?php
// database/migrations/[timestamp]_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();

            // Define foreign keys properly
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('user_id')->nullable();

            // Order details
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            // Files
            $table->json('file_attachments')->nullable();
            $table->string('file_paths')->nullable();

            // Requirements
            $table->json('print_requirements')->nullable();

            // Communication
            $table->string('source_channel'); // telegram, whatsapp, web, walk-in
            $table->string('source_channel_id')->nullable(); // original message ID

            // Dates
            $table->timestamp('order_date');
            $table->timestamp('required_by')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Pricing
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('currency')->default('ETB');

            // Payment
            $table->enum('payment_status', [
                'pending', 'partial', 'paid', 'verified', 'refunded'
            ])->default('pending');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_verified_at')->nullable();

            // Status
            $table->enum('status', [
                'draft', 'submitted', 'approved', 'processing',
                'printing', 'quality_check', 'completed',
                'delivered', 'cancelled', 'rejected'
            ])->default('draft');

            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->json('metadata')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Add indexes
            $table->index(['customer_id', 'order_date']);
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_number');
        });

        // Add foreign keys after table creation
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('restrict');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->foreign('verified_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

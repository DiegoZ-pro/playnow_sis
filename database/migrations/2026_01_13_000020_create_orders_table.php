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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('order_number', 50)->unique();
            $table->enum('status', [
                'pending', 
                'confirmed', 
                'preparing', 
                'shipped', 
                'delivered', 
                'cancelled'
            ])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('whatsapp_sent')->default(false);
            $table->timestamps();
            
            $table->index('order_number');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
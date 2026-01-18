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
        Schema::table('orders', function (Blueprint $table) {
            
            // Agregar campos solo si no existen
            
            if (!Schema::hasColumn('orders', 'delivery_address')) {
                $table->string('delivery_address', 500)->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'order_status')) {
                $table->enum('order_status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])
                      ->default('pending');
            }
            
            if (!Schema::hasColumn('orders', 'payment_proof')) {
                $table->string('payment_proof')->nullable();
            }
            
            if (!Schema::hasColumn('orders', 'shipping')) {
                $table->decimal('shipping', 10, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'delivery_address',
                'delivery_notes',
                'order_status',
                'payment_proof',
                'shipping',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
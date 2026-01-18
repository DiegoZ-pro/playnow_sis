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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            
            // Para usuarios invitados (sin login)
            $table->string('session_id', 255)->nullable()->index();
            
            // Para usuarios registrados (con login)
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            
            // Relación con variante del producto
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            
            // Cantidad de productos
            $table->integer('quantity')->default(1);
            
            // Timestamps
            $table->timestamps();
            
            // Índices compuestos para búsquedas rápidas
            $table->index(['session_id', 'product_variant_id']);
            $table->index(['customer_id', 'product_variant_id']);
            
            // Constraint: no puede haber duplicados de la misma variante para el mismo usuario/session
            $table->unique(['session_id', 'product_variant_id'], 'unique_session_variant');
            $table->unique(['customer_id', 'product_variant_id'], 'unique_customer_variant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
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
        Schema::table('product_images', function (Blueprint $table) {
            // Agregar campo order si no existe (para ordenar las imágenes)
            if (!Schema::hasColumn('product_images', 'order')) {
                $table->integer('order')->default(0)->after('image_url');
            }
            
            // Agregar campo is_primary si no existe (marcar imagen principal)
            if (!Schema::hasColumn('product_images', 'is_primary')) {
                $table->boolean('is_primary')->default(false)->after('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            if (Schema::hasColumn('product_images', 'order')) {
                $table->dropColumn('order');
            }
            if (Schema::hasColumn('product_images', 'is_primary')) {
                $table->dropColumn('is_primary');
            }
        });
    }
};
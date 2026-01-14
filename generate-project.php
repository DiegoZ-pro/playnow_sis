<?php

/**
 * PLAY NOW - Generador Automático de Archivos
 * 
 * Este script crea todos los archivos necesarios para el proyecto
 * 
 * INSTRUCCIONES:
 * 1. Crear proyecto Laravel: composer create-project laravel/laravel playnow
 * 2. Colocar este archivo en la raíz: /playnow/generate-project.php
 * 3. Ejecutar: php generate-project.php
 */

echo "========================================\n";
echo "  PLAY NOW - Generador de Archivos\n";
echo "========================================\n\n";

// Verificar que estamos en un proyecto Laravel
if (!file_exists('artisan')) {
    die("ERROR: Este script debe ejecutarse desde la raíz de Laravel\n");
}

echo "Generando archivos del proyecto...\n\n";

// LISTA DE COMANDOS PARA CREAR ARCHIVOS
$commands = [
    // Middleware
    "php artisan make:middleware CheckSubdomain",
    "php artisan make:middleware CheckRole",
    
    // Migraciones
    "php artisan make:migration create_roles_table",
    "php artisan make:migration create_categories_table",
    "php artisan make:migration create_brands_table",
    "php artisan make:migration create_sizes_table",
    "php artisan make:migration create_colors_table",
    "php artisan make:migration create_products_table",
    "php artisan make:migration create_product_images_table",
    "php artisan make:migration create_product_variants_table",
    "php artisan make:migration create_customers_table",
    "php artisan make:migration create_orders_table",
    "php artisan make:migration create_order_details_table",
    "php artisan make:migration create_sales_table",
    "php artisan make:migration create_sale_details_table",
    "php artisan make:migration create_stock_movements_table",
    
    // Seeders
    "php artisan make:seeder RoleSeeder",
    "php artisan make:seeder UserSeeder",
    "php artisan make:seeder CategorySeeder",
    "php artisan make:seeder BrandSeeder",
    "php artisan make:seeder SizeSeeder",
    "php artisan make:seeder ColorSeeder",
    
    // Modelos
    "php artisan make:model Role",
    "php artisan make:model Category",
    "php artisan make:model Brand",
    "php artisan make:model Size",
    "php artisan make:model Color",
    "php artisan make:model Product",
    "php artisan make:model ProductImage",
    "php artisan make:model ProductVariant",
    "php artisan make:model Customer",
    "php artisan make:model Order",
    "php artisan make:model OrderDetail",
    "php artisan make:model Sale",
    "php artisan make:model SaleDetail",
    "php artisan make:model StockMovement",
];

echo "Ejecutando comandos Artisan...\n";
foreach ($commands as $command) {
    echo "  → " . $command . "\n";
    exec($command);
}

echo "\n✓ Archivos base creados correctamente\n";
echo "\nAHORA DEBES:\n";
echo "1. Editar cada archivo con el contenido proporcionado\n";
echo "2. Configurar el archivo .env\n";
echo "3. Ejecutar: php artisan migrate --seed\n";
echo "4. Configurar hosts del sistema\n\n";

echo "========================================\n";
echo "Ver documentación completa en:\n";
echo "- README.md\n";
echo "- INSTALL.md\n";
echo "- GUIA_INSTALACION_COMPLETA.md\n";
echo "========================================\n";
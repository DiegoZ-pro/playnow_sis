<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageService
{
    /**
     * Upload and process product image
     *
     * @param UploadedFile $file
     * @param int $productId
     * @return string Path to stored image
     */
    public function uploadProductImage(UploadedFile $file, int $productId): string
    {
        // Generar nombre único
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'products/' . $productId . '/' . $filename;
        
        // Verificar si Intervention Image está disponible
        if (class_exists('\Intervention\Image\ImageManager')) {
            // Usar Intervention Image para redimensionar
            try {
                // Crear manager con configuración
                $config = ['driver' => 'gd'];
                $manager = \Intervention\Image\ImageManager::gd(); // Laravel 12 / Intervention 3.x
                
                $image = $manager->read($file->getRealPath());
                
                // Si la imagen es más grande que 1200px, redimensionar
                if ($image->width() > 1200 || $image->height() > 1200) {
                    $image->scale(1200, 1200);
                }
                
                // Guardar en storage/app/public/products/{product_id}/
                $encoded = $image->encode();
                Storage::disk('public')->put($path, (string) $encoded);
                
            } catch (\Exception $e) {
                // Si falla Intervention, guardar sin redimensionar
                Storage::disk('public')->putFileAs(
                    'products/' . $productId,
                    $file,
                    $filename
                );
            }
        } else {
            // Sin Intervention Image, guardar directamente
            Storage::disk('public')->putFileAs(
                'products/' . $productId,
                $file,
                $filename
            );
        }
        
        return $path;
    }

    /**
     * Delete image from storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteImage(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Validate image file
     *
     * @param UploadedFile $file
     * @return array
     */
    public function validateImage(UploadedFile $file): array
    {
        $errors = [];
        
        // Validar tamaño (max 5MB)
        if ($file->getSize() > 5242880) { // 5MB en bytes
            $errors[] = 'La imagen excede el tamaño máximo de 5MB';
        }
        
        // Validar tipo de archivo
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Formato de imagen no permitido. Use JPG, PNG o WEBP';
        }
        
        return $errors;
    }
}
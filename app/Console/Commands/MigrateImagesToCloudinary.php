<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\GcashSettings;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToCloudinary extends Command
{
    protected $signature = 'images:migrate-to-cloudinary';
    protected $description = 'Migrate existing local images to Cloudinary';

    public function handle()
    {
        $this->info('Starting image migration to Cloudinary...');

        $cloudinary = new Cloudinary();
        $migratedCount = 0;
        $errorCount = 0;

        // Migrate Product images
        $this->info('Migrating product images...');
        $products = Product::whereNotNull('image_path')
            ->where('image_path', 'not like', 'http%')
            ->get();

        foreach ($products as $product) {
            try {
                $localPath = $product->image_path;
                $fullPath = storage_path('app/public/' . $localPath);

                if (!file_exists($fullPath)) {
                    $this->warn("File not found: {$fullPath}");
                    $errorCount++;
                    continue;
                }

                // Upload to Cloudinary
                $result = $cloudinary->uploadApi()->upload($fullPath, [
                    'folder' => 'flexispace/products',
                    'resource_type' => 'image',
                    'public_id' => 'product_' . $product->id . '_' . time(),
                ]);

                // Update product with Cloudinary URL
                $product->image_path = $result['secure_url'];
                $product->save();

                $this->info("Migrated product {$product->id}: {$result['secure_url']}");
                $migratedCount++;

                // Optional: Delete local file after successful upload
                // Storage::disk('public')->delete($localPath);

            } catch (\Exception $e) {
                $this->error("Failed to migrate product {$product->id}: " . $e->getMessage());
                $errorCount++;
            }
        }

        // Migrate GCash QR code
        $this->info('Migrating GCash QR code...');
        $settings = GcashSettings::first();

        if ($settings && $settings->gcash_qr_code && !str_starts_with($settings->gcash_qr_code, 'http')) {
            try {
                $fullPath = storage_path('app/public/' . $settings->gcash_qr_code);

                if (file_exists($fullPath)) {
                    $result = $cloudinary->uploadApi()->upload($fullPath, [
                        'folder' => 'flexispace/gcash-qr',
                        'resource_type' => 'image',
                        'public_id' => 'gcash_qr_' . time(),
                    ]);

                    $settings->gcash_qr_code = $result['secure_url'];
                    $settings->save();

                    $this->info("Migrated GCash QR code: {$result['secure_url']}");
                    $migratedCount++;
                } else {
                    $this->warn("GCash QR file not found: {$fullPath}");
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to migrate GCash QR: " . $e->getMessage());
                $errorCount++;
            }
        }

        $this->info("Migration complete!");
        $this->info("Successfully migrated: {$migratedCount}");
        $this->info("Errors: {$errorCount}");

        return $errorCount > 0 ? 1 : 0;
    }
}

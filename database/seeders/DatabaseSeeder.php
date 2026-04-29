<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin account
        \App\Models\User::create([
            'name' => 'Admin FlexiSpace',
            'first_name' => 'Admin',
            'last_name' => 'FlexiSpace',
            'email' => 'admin@flexispace.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Sample customer account
        \App\Models\User::create([
            'name' => 'John Doe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'customer@flexispace.com',
            'password' => bcrypt('customer123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // Seed products
        $products = [
            ['name' => 'Transformer Dining Table', 'description' => 'Expands from 4 to 10 seats', 'price' => 160900, 'stock' => 15, 'category' => 'Dining', 'is_active' => true],
            ['name' => 'Modular Sofa Set', 'description' => 'Configurable L-shape or straight sofa', 'price' => 85000, 'stock' => 10, 'category' => 'Living', 'is_active' => true],
            ['name' => 'Space-Saving Desk', 'description' => 'Foldable home office desk', 'price' => 32000, 'stock' => 20, 'category' => 'Office', 'is_active' => true],
            ['name' => 'Storage Ottoman', 'description' => 'Dual purpose seat and storage', 'price' => 18500, 'stock' => 30, 'category' => 'Living', 'is_active' => true],
            ['name' => 'Wall Bed Cabinet', 'description' => 'Murphy bed with built-in cabinet', 'price' => 95000, 'stock' => 8, 'category' => 'Bedroom', 'is_active' => true],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}

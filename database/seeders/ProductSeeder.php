<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Modular Sofa Set',
                'description' => 'A versatile modular sofa that can be configured to fit any space. Features premium fabric upholstery and sturdy wooden frame.',
                'price' => 899.99,
                'stock' => 15,
                'category' => 'Sofas',
                'is_active' => true,
            ],
            [
                'name' => 'Adjustable Standing Desk',
                'description' => 'Electric height-adjustable desk with memory presets. Perfect for home offices with ergonomic design.',
                'price' => 549.99,
                'stock' => 20,
                'category' => 'Desks',
                'is_active' => true,
            ],
            [
                'name' => 'Storage Ottoman',
                'description' => 'Multi-functional ottoman with hidden storage compartment. Can be used as footrest or extra seating.',
                'price' => 199.99,
                'stock' => 30,
                'category' => 'Storage',
                'is_active' => true,
            ],
            [
                'name' => 'Wall-Mounted Shelving Unit',
                'description' => 'Modern floating shelves with modular design. Easy to install and perfect for displaying books and decor.',
                'price' => 149.99,
                'stock' => 25,
                'category' => 'Shelves',
                'is_active' => true,
            ],
            [
                'name' => 'Folding Room Divider',
                'description' => 'Portable room divider with 4 panels. Creates instant privacy in open spaces. Lightweight and easy to move.',
                'price' => 299.99,
                'stock' => 18,
                'category' => 'Dividers',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

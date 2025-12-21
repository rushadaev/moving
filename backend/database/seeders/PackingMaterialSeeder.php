<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PackingMaterial;

class PackingMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = [
            // Full Service Packing (special option)
            [
                'name' => 'full_service_packing',
                'display_name' => 'Full Service Packing',
                'price' => 200.00,
                'icon' => 'Packing.svg',
                'description' => 'Complete packing service including all materials',
                'is_active' => true,
                'is_full_service' => true,
                'order' => 0,
            ],

            // Individual Materials
            [
                'name' => 'shrink_wrap',
                'display_name' => 'Shrink Wrap',
                'price' => 15.00,
                'icon' => 'Packing.svg',
                'description' => 'Protective shrink wrap for furniture',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 1,
            ],
            [
                'name' => 'corrugated_cardboard',
                'display_name' => 'Corrugated Cardboard',
                'price' => 8.00,
                'icon' => 'Packing.svg',
                'description' => 'Heavy-duty cardboard sheets',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 2,
            ],
            [
                'name' => 'small_boxes',
                'display_name' => 'Small Boxes',
                'price' => 3.00,
                'icon' => 'Packing.svg',
                'description' => 'Small moving boxes (per box)',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 3,
            ],
            [
                'name' => 'medium_boxes',
                'display_name' => 'Medium Boxes',
                'price' => 5.00,
                'icon' => 'Packing.svg',
                'description' => 'Medium moving boxes (per box)',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 4,
            ],
            [
                'name' => 'large_boxes',
                'display_name' => 'Large Boxes',
                'price' => 7.00,
                'icon' => 'Packing.svg',
                'description' => 'Large moving boxes (per box)',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 5,
            ],
            [
                'name' => 'wardrobe_boxes',
                'display_name' => 'Wardrobe Boxes',
                'price' => 12.00,
                'icon' => 'Packing.svg',
                'description' => 'Wardrobe boxes with hanging bar',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 6,
            ],
            [
                'name' => 'plastic_tape',
                'display_name' => 'Plastic Tape',
                'price' => 4.00,
                'icon' => 'Packing.svg',
                'description' => 'Heavy-duty plastic packing tape',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 7,
            ],
            [
                'name' => 'bubble_wrap',
                'display_name' => 'Bubble Wrap',
                'price' => 10.00,
                'icon' => 'Packing.svg',
                'description' => 'Protective bubble wrap roll',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 8,
            ],
            [
                'name' => 'moving_tape',
                'display_name' => 'Moving Tape',
                'price' => 3.00,
                'icon' => 'Packing.svg',
                'description' => 'Professional moving tape',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 9,
            ],
            [
                'name' => 'paper_wrap',
                'display_name' => 'Paper Wrap',
                'price' => 6.00,
                'icon' => 'Packing.svg',
                'description' => 'Packing paper for fragile items',
                'is_active' => true,
                'is_full_service' => false,
                'order' => 10,
            ],
        ];

        foreach ($materials as $material) {
            PackingMaterial::create($material);
        }

        $this->command->info('Packing materials seeded successfully!');
    }
}

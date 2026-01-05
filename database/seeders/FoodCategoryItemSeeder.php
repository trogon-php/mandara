<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FoodCategory;
use App\Models\FoodItem;
use App\Models\FoodMenu;
use Carbon\Carbon;

class FoodCategoryItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating food categories and items...');

        // Define categories with their time ranges
        $categories = [
            [
                'title' => 'Breakfast',
                'description' => 'Morning meal items',
                'start_time' => '07:00',
                'end_time' => '09:00',
                'sort_order' => 1,
                'status' => true,
                'items' => [
                    [
                        'title' => 'Chappati',
                        'short_description' => 'Fresh homemade chappati',
                        'description' => '2 pieces of freshly made chappati, perfect for breakfast',
                        'price' => 100.00,
                        'is_veg' => 1,
                        'stock' => 50,
                        'status' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Bread',
                        'short_description' => 'Fresh bread slices',
                        'description' => '2 pieces of soft white bread',
                        'price' => 50.00,
                        'is_veg' => 1,
                        'stock' => 100,
                        'status' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Milk',
                        'short_description' => 'Fresh milk',
                        'description' => '1 cup of fresh milk',
                        'price' => 60.00,
                        'is_veg' => 1,
                        'stock' => 80,
                        'status' => true,
                        'sort_order' => 3,
                    ],
                    [
                        'title' => 'Omelette',
                        'short_description' => 'Fresh egg omelette',
                        'description' => '2 egg omelette with vegetables',
                        'price' => 120.00,
                        'is_veg' => 0,
                        'stock' => 40,
                        'status' => true,
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Lunch',
                'description' => 'Midday meal items',
                'start_time' => '12:00',
                'end_time' => '14:00',
                'sort_order' => 2,
                'status' => true,
                'items' => [
                    [
                        'title' => 'Rice',
                        'short_description' => 'Steamed basmati rice',
                        'description' => '1 cup of steamed basmati rice',
                        'price' => 80.00,
                        'is_veg' => 1,
                        'stock' => 200,
                        'status' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Fish Curry',
                        'short_description' => 'Spicy fish curry',
                        'description' => '1 plate of fresh fish curry with spices',
                        'price' => 250.00,
                        'is_veg' => 0,
                        'stock' => 30,
                        'status' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Dal',
                        'short_description' => 'Lentil curry',
                        'description' => '1 bowl of mixed dal curry',
                        'price' => 100.00,
                        'is_veg' => 1,
                        'stock' => 150,
                        'status' => true,
                        'sort_order' => 3,
                    ],
                    [
                        'title' => 'Vegetable Curry',
                        'short_description' => 'Mixed vegetable curry',
                        'description' => '1 plate of mixed seasonal vegetables',
                        'price' => 120.00,
                        'is_veg' => 1,
                        'stock' => 100,
                        'status' => true,
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Snacks',
                'description' => 'Evening snack items',
                'start_time' => '16:00',
                'end_time' => '18:00',
                'sort_order' => 3,
                'status' => true,
                'items' => [
                    [
                        'title' => 'Oats',
                        'short_description' => 'Healthy oats bowl',
                        'description' => '1 bowl of nutritious oats with fruits',
                        'price' => 90.00,
                        'is_veg' => 1,
                        'stock' => 60,
                        'status' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Samosa',
                        'short_description' => 'Crispy samosa',
                        'description' => '2 pieces of crispy vegetable samosa',
                        'price' => 70.00,
                        'is_veg' => 1,
                        'stock' => 80,
                        'status' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Tea',
                        'short_description' => 'Hot tea',
                        'description' => '1 cup of hot tea',
                        'price' => 40.00,
                        'is_veg' => 1,
                        'stock' => 200,
                        'status' => true,
                        'sort_order' => 3,
                    ],
                    [
                        'title' => 'Pakora',
                        'short_description' => 'Fried pakora',
                        'description' => '5 pieces of mixed vegetable pakora',
                        'price' => 80.00,
                        'is_veg' => 1,
                        'stock' => 70,
                        'status' => true,
                        'sort_order' => 4,
                    ],
                ],
            ],
            [
                'title' => 'Dinner',
                'description' => 'Evening meal items',
                'start_time' => '18:00',
                'end_time' => '20:00',
                'sort_order' => 4,
                'status' => true,
                'items' => [
                    [
                        'title' => 'Rice',
                        'short_description' => 'Steamed basmati rice',
                        'description' => '1 cup of steamed basmati rice',
                        'price' => 80.00,
                        'is_veg' => 1,
                        'stock' => 200,
                        'status' => true,
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Chicken Curry',
                        'short_description' => 'Spicy chicken curry',
                        'description' => '1 plate of tender chicken curry',
                        'price' => 280.00,
                        'is_veg' => 0,
                        'stock' => 35,
                        'status' => true,
                        'sort_order' => 2,
                    ],
                    [
                        'title' => 'Roti',
                        'short_description' => 'Fresh roti',
                        'description' => '2 pieces of fresh roti',
                        'price' => 60.00,
                        'is_veg' => 1,
                        'stock' => 120,
                        'status' => true,
                        'sort_order' => 3,
                    ],
                    [
                        'title' => 'Paneer Curry',
                        'short_description' => 'Creamy paneer curry',
                        'description' => '1 plate of creamy paneer curry',
                        'price' => 200.00,
                        'is_veg' => 1,
                        'stock' => 50,
                        'status' => true,
                        'sort_order' => 4,
                    ],
                ],
            ],
        ];

        // Create categories and their items
        $allItems = [];
        foreach ($categories as $categoryData) {
            $items = $categoryData['items'];
            unset($categoryData['items']);

            // Create category
            $category = FoodCategory::create($categoryData);
            $this->command->info("Created category: {$category->title} (ID: {$category->id})");

            // Create items for this category
            foreach ($items as $itemData) {
                $itemData['category_id'] = $category->id;
                $item = FoodItem::create($itemData);
                $allItems[] = $item;
                $this->command->info("  Created item: {$item->title} (ID: {$item->id})");
            }
        }

        // Create food menu entries for the next 7 days
        $this->command->info('Creating food menu entries...');
        $menuDates = [];
        for ($i = 0; $i < 7; $i++) {
            $menuDates[] = Carbon::today()->addDays($i)->format('Y-m-d');
        }

        $menuCount = 0;
        foreach ($menuDates as $menuDate) {
            $sortOrder = 1;
            foreach ($allItems as $item) {
                FoodMenu::create([
                    'food_item_id' => $item->id,
                    'menu_date' => $menuDate,
                    'sort_order' => $sortOrder++,
                ]);
                $menuCount++;
            }
            $this->command->info("  Created menu entries for date: {$menuDate}");
        }

        $this->command->info('Food categories and items seeded successfully!');
        $this->command->info("Total menu entries created: {$menuCount}");
    }
}


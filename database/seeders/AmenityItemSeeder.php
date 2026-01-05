<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AmenityItem;
use App\Models\PackageAmenityItem;
use App\Models\CottagePackage;
use App\Models\CottageCategory;

class AmenityItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenityIds = [2, 3, 4];
        
        // Verify amenities exist
        foreach ($amenityIds as $amenityId) {
            $amenity = \App\Models\Amenity::find($amenityId);
            if (!$amenity) {
                $this->command->warn("Amenity with ID {$amenityId} not found. Skipping...");
                continue;
            }
        }

        // Get or create test packages
        $packages = $this->getOrCreateTestPackages();

        // Create amenity items for each amenity with various test cases
        $amenityItems = [];

        foreach ($amenityIds as $amenityId) {
            // Test Case 1: Active item with price and description
            $amenityItems[] = [
                'amenity_id' => $amenityId,
                'title' => "Premium Service - Amenity {$amenityId}",
                'description' => "This is a premium service option with full features and support. Perfect for users who want the complete experience.",
                'duration_minutes' => 60,
                'duration_text' => '1 hour',
                'price' => 150.00,
                'status' => 'active',
            ];

            // Test Case 2: Active item with zero price (free/included)
            $amenityItems[] = [
                'amenity_id' => $amenityId,
                'title' => "Basic Service - Amenity {$amenityId}",
                'description' => "A basic service option that is included in packages. No additional charge required.",
                'duration_minutes' => 30,
                'duration_text' => '30 minutes',
                'price' => 0.00,
                'status' => 'active',
            ];

            // Test Case 3: Active item with low price
            $amenityItems[] = [
                'amenity_id' => $amenityId,
                'title' => "Standard Service - Amenity {$amenityId}",
                'description' => "Standard service option with moderate pricing. Good value for money.",
                'duration_minutes' => 45,
                'duration_text' => '45 minutes',
                'price' => 50.00,
                'status' => 'active',
            ];

            // Test Case 4: Active item with high price
            $amenityItems[] = [
                'amenity_id' => $amenityId,
                'title' => "Deluxe Service - Amenity {$amenityId}",
                'description' => "Deluxe service with premium features and extended duration. Includes all premium amenities.",
                'duration_minutes' => 120,
                'duration_text' => '2 hours',
                'price' => 300.00,
                'status' => 'active',
            ];

            // Test Case 5: Inactive item (for testing inactive status)
            $amenityItems[] = [
                'amenity_id' => $amenityId,
                'title' => "Legacy Service - Amenity {$amenityId}",
                'description' => "This service is no longer available but kept for historical records.",
                'duration_minutes' => 90,
                'duration_text' => '1.5 hours',
                'price' => 200.00,
                'status' => 'inactive',
            ];

            // Test Case 6: Active item without description
            $amenityItems[] = [
                'amenity_id' => $amenityId,
                'title' => "Quick Service - Amenity {$amenityId}",
                'description' => null,
                'duration_minutes' => 15,
                'duration_text' => '15 minutes',
                'price' => 25.00,
                'status' => 'active',
            ];

            // Test Case 7: Active item with medium price and custom duration text
            $amenityItems[] = [
                'amenity_id' => $amenityId,
                'title' => "Extended Service - Amenity {$amenityId}",
                'description' => "Extended duration service for users who need more time. Includes refreshments and additional amenities.",
                'duration_minutes' => 180,
                'duration_text' => '3 hours (with break)',
                'price' => 250.00,
                'status' => 'active',
            ];

            // Test Case 8: Active item with fractional price
            $amenityItems[] = [
                'amenity_id' => $amenityId,
                'title' => "Budget Service - Amenity {$amenityId}",
                'description' => "Affordable option for budget-conscious users. All essential features included.",
                'duration_minutes' => 20,
                'duration_text' => '20 minutes',
                'price' => 19.99,
                'status' => 'active',
            ];
        }

        // Insert amenity items
        $createdItems = [];
        foreach ($amenityItems as $itemData) {
            $item = AmenityItem::create($itemData);
            $createdItems[] = $item;
            $this->command->info("Created amenity item: {$item->title} (ID: {$item->id})");
        }

        // Create package-amenity item relationships
        $this->createPackageRelationships($createdItems, $packages);

        $this->command->info('Amenity items and package relationships seeded successfully!');
        $this->command->info("Total amenity items created: " . count($createdItems));
    }

    /**
     * Get or create test packages for linking amenity items
     */
    private function getOrCreateTestPackages(): array
    {
        // Try to get existing packages
        $packages = CottagePackage::take(3)->get();

        // If no packages exist, create some test packages
        if ($packages->isEmpty()) {
            $this->command->info('No existing packages found. Creating test packages...');
            
            // Get or create a cottage category
            $category = CottageCategory::first();
            if (!$category) {
                $category = CottageCategory::create([
                    'title' => 'Test Category',
                    'description' => 'Test category for seeder',
                    'status' => 'active',
                ]);
            }

            $packages = [];
            $packageData = [
                [
                    'title' => 'Basic Package',
                    'description' => 'Basic package with essential amenities',
                    'cottage_category_id' => $category->id,
                    'price' => 500.00,
                    'discount_amount' => 0,
                    'booking_amount' => 100.00,
                    'tax_included' => false,
                    'duration_days' => 1,
                    'status' => 'active',
                ],
                [
                    'title' => 'Premium Package',
                    'description' => 'Premium package with all amenities included',
                    'cottage_category_id' => $category->id,
                    'price' => 1000.00,
                    'discount_amount' => 100.00,
                    'booking_amount' => 200.00,
                    'tax_included' => true,
                    'duration_days' => 3,
                    'status' => 'active',
                ],
                [
                    'title' => 'Deluxe Package',
                    'description' => 'Deluxe package with premium amenities and extended duration',
                    'cottage_category_id' => $category->id,
                    'price' => 2000.00,
                    'discount_amount' => 200.00,
                    'booking_amount' => 500.00,
                    'tax_included' => true,
                    'duration_days' => 7,
                    'status' => 'active',
                ],
            ];

            foreach ($packageData as $data) {
                $package = CottagePackage::create($data);
                $packages[] = $package;
                $this->command->info("Created test package: {$package->title} (ID: {$package->id})");
            }
        }

        return $packages->all();
    }

    /**
     * Create relationships between packages and amenity items
     */
    private function createPackageRelationships(array $amenityItems, array $packages): void
    {
        if (empty($packages)) {
            $this->command->warn('No packages available to create relationships.');
            return;
        }

        $relationshipsCreated = 0;

        // Link items to packages with different scenarios
        foreach ($amenityItems as $index => $item) {
            // Scenario 1: Link some items to first package (Basic)
            if ($index % 4 === 0 && isset($packages[0])) {
                PackageAmenityItem::create([
                    'package_id' => $packages[0]->id,
                    'amenity_item_id' => $item->id,
                ]);
                $relationshipsCreated++;
            }

            // Scenario 2: Link some items to second package (Premium)
            if ($index % 3 === 0 && isset($packages[1])) {
                PackageAmenityItem::create([
                    'package_id' => $packages[1]->id,
                    'amenity_item_id' => $item->id,
                ]);
                $relationshipsCreated++;
            }

            // Scenario 3: Link some items to third package (Deluxe)
            if ($index % 2 === 0 && isset($packages[2])) {
                PackageAmenityItem::create([
                    'package_id' => $packages[2]->id,
                    'amenity_item_id' => $item->id,
                ]);
                $relationshipsCreated++;
            }

            // Scenario 4: Link free items (price = 0) to all packages
            if ($item->price == 0) {
                foreach ($packages as $package) {
                    // Check if relationship already exists
                    $exists = PackageAmenityItem::where('package_id', $package->id)
                        ->where('amenity_item_id', $item->id)
                        ->exists();
                    
                    if (!$exists) {
                        PackageAmenityItem::create([
                            'package_id' => $package->id,
                            'amenity_item_id' => $item->id,
                        ]);
                        $relationshipsCreated++;
                    }
                }
            }
        }

        $this->command->info("Created {$relationshipsCreated} package-amenity item relationships.");
    }
}
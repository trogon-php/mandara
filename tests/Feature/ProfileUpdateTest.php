<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileUpdateTest extends TestCase
{
    public function test_profile_update_without_image()
    {
        // Create a test user
        $user = User::factory()->create([
            'role_id' => 2, // Student role
            'status' => 'active'
        ]);

        // Authenticate the user
        $this->actingAs($user, 'api');

        // Test data
        $updateData = [
            'name' => 'Updated Name',
            'country_code' => '+1',
            'phone' => '1234567890'
        ];

        // Make the API request
        $response = $this->putJson('/api/v1/profile', $updateData);

        // Assert the response
        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'Profile updated successfully'
                ])
                ->assertJsonStructure([
                    'status',
                    'http_code',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'phone',
                        'country_code',
                        'profile_image_url'
                    ]
                ]);

        // Verify the user was updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'country_code' => '+1',
            'phone' => '1234567890'
        ]);
    }

    public function test_profile_update_with_image()
    {
        // Create a test user
        $user = User::factory()->create([
            'role_id' => 2, // Student role
            'status' => 'active'
        ]);

        // Authenticate the user
        $this->actingAs($user, 'api');

        // Create a fake image file
        Storage::fake('public');
        $image = UploadedFile::fake()->image('profile.jpg', 100, 100);

        // Test data
        $updateData = [
            'name' => 'Updated Name',
            'profile_picture' => $image
        ];

        // Make the API request
        $response = $this->putJson('/api/v1/profile', $updateData);

        // Assert the response
        $response->assertStatus(200)
                ->assertJson([
                    'status' => true,
                    'message' => 'Profile updated successfully'
                ]);

        // Verify the user was updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name'
        ]);
    }

    public function test_profile_update_validation_errors()
    {
        // Create a test user
        $user = User::factory()->create([
            'role_id' => 2, // Student role
            'status' => 'active'
        ]);

        // Authenticate the user
        $this->actingAs($user, 'api');

        // Test data with invalid values
        $updateData = [
            'name' => str_repeat('a', 300), // Too long
            'country_code' => '123456', // Too long
            'phone' => str_repeat('1', 25), // Too long
        ];

        // Make the API request
        $response = $this->putJson('/api/v1/profile', $updateData);

        // Assert validation errors
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'country_code', 'phone']);
    }
}

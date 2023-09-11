<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTravelTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_cannot_access_adding_travel_endpoint(): void
    {
        $response = $this->postJson("/api/v1/admin/travels");
        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_adding_travel_endpoint(): void
    {
        // Given
        // NOTE: RefreshDatabase Trait won't seed any data. Launch the seeder manually
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where("name", "editor")->value("id"));

        // When 
        $response = $this->actingAs($user)->postJson("/api/v1/admin/travels");

        // Assert
        $response->assertStatus(403);
    }

    public function test_saves_travel_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where("name", "admin")->value("id"));

        $response = $this->actingAs($user)->postJson("/api/v1/admin/travels", [
            "name" => "Test travel name one"
        ]);

        // Test validation works. By first failing login check.
        // 422 means Unprocessable Entity. SInce not all required fields were provided
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson("/api/v1/admin/travels", [
            "name" => "Test travel name two",
            "is_public" => 1,
            "description" => "Some description",
            "number_of_days" => 5
        ]);

        // Status 201 represents "Created" successfully after POST
        $response->assertStatus(201);

        $response = $this->get("/api/v1/travels");
        $response->assertJsonFragment(["name" => "Test travel name two"]);
    }
}
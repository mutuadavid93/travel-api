<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Role, User, Travel};
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_cannot_access_adding_tour_endpoint(): void
    {
        // Given: a travel
        $travel = Travel::factory()->create();

        // When: you try to add a tour without any user
        $response = $this->postJson("/api/v1/admin/travels/{$travel->id}/tours");

        // Assert: unauthorized
        $response->assertStatus(401);
    }

    public function test_non_admin_user_cannot_access_adding_tour_endpoint(): void
    {
        // Given: a user with a non-admin role and a travel
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where("name", "editor")->value("id"));
        $travel = Travel::factory()->create();

        // When: try to create tour
        $response = $this->actingAs($user)->postJson("/api/v1/admin/travels/{$travel->id}/tours", [
            "name" => "Tour name"
        ]);

        // Assert:
        $response->assertStatus(403);
    }

    public function test_saves_tour_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->roles()->attach(Role::where("name", "admin")->value("id"));
        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/v1/admin/travels/{$travel->id}/tours", [
            "name" => "Test tour name one"
        ]);

        // 422 means Unprocessable Entity. SInce not all required fields were provided
        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson("/api/v1/admin/travels/{$travel->id}/tours", [
            "name" => "Test tour name two",
            "starting_date" => now()->toDateString(),
            "ending_date" => now()->addDay()->toDateString(),
            "price" => "909.99",
        ]);

        // Status 201 represents "Created" successfully after POST
        $response->assertStatus(201);

        $response = $this->get("/api/v1/travels/{$travel->slug}/tours");
        $response->assertJsonFragment(["name" => "Test tour name two"]);
    }
}

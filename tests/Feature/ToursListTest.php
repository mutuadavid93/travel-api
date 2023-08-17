<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Travel, Tour};
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ToursListTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_list_by_travel_slug_returns_correct_tours(): void
    {
        // Given
        // Seed a travel and relate it to a tour
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create(["travel_id" => $travel->id]);

        // When
        $response = $this->get("/api/v1/travels/{$travel->slug}/tours");

        // Then
        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");
        $response->assertJsonFragment(["id" => $tour->id]);
    }

    public function test_tour_price_is_shown_correctly(): void
    {
        $travel = Travel::factory()->create();
        $tour = Tour::factory()->create([
            "travel_id" => $travel->id,
            "price" => 123.45
        ]);

        $response = $this->get("/api/v1/travels/{$travel->slug}/tours");

        $response->assertStatus(200);
        $response->assertJsonCount(1, "data");

        // NOTE: the price is in json thus it's quoted
        $response->assertJsonFragment(["price" => "123.45"]);
    }

    public function test_tours_list_returns_pagination(): void
    {
        $travel = Travel::factory()->create();
        // create 16 tour records all related to a single travel
        Tour::factory(16)->create(["travel_id" => $travel->id]);

        $response = $this->get("/api/v1/travels/{$travel->slug}/tours");

        $response->assertStatus(200);
        $response->assertJsonCount(15, "data");
        $response->assertJsonPath("meta.last_page", 2);
    }
}

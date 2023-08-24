<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{Travel, Tour};
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

    public function test_tours_list_sorts_by_starting_date_correctly(): void
    {
        // Given below tours i.e. $lateTour and $earlierTour
        $travel = Travel::factory()->create();
        $futureTour = Tour::factory()->create([
            // Override default seeder data
            "travel_id" => $travel->id,
            "starting_date" => now()->addDays(2),
            "ending_date" => now()->addDays(3),
        ]);
        $earlierTour = Tour::factory()->create([
            // Override default seeder data
            "travel_id" => $travel->id,
            "starting_date" => now(),
            "ending_date" => now()->addDays(1),
        ]);

        // When queried
        $response = $this->get("/api/v1/travels/{$travel->slug}/tours");

        // Assert. 
        $response->assertStatus(200);

        // By default is ascending order so dates in the future come second
        $response->assertJsonPath("data.0.id", $earlierTour->id);
        $response->assertJsonPath("data.1.id", $futureTour->id);
    }

    public function test_tours_list_sorts_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            // Override default seeder data
            "travel_id" => $travel->id,
            "price" => 200
        ]);

        // NOTE: orderBy starting_date test is also done here
        $cheapFutureTour = Tour::factory()->create([
            "travel_id" => $travel->id,
            "price" => 100,
            "starting_date" => now()->addDays(2),
            "ending_date" => now()->addDays(3),
        ]);
        $cheapEarlierTour = Tour::factory()->create([
            "travel_id" => $travel->id,
            "price" => 100,
            "starting_date" => now(),
            "ending_date" => now()->addDays(1),
        ]);

        $response = $this->get("/api/v1/travels/{$travel->slug}/tours?sortBy=price&sortOrder=asc");

        $response->assertStatus(200);
        $response->assertJsonPath("data.0.id", $cheapEarlierTour->id);
        $response->assertJsonPath("data.1.id", $cheapFutureTour->id);
        $response->assertJsonPath("data.2.id", $expensiveTour->id);
    }

    public function test_tours_list_filters_by_price_correctly(): void
    {
        $travel = Travel::factory()->create();
        $expensiveTour = Tour::factory()->create([
            "travel_id" => $travel->id,
            "price" => 200
        ]);
        $cheapTour = Tour::factory()->create([
            "travel_id" => $travel->id,
            "price" => 100
        ]);

        $endpoint = "/api/v1/travels/{$travel->slug}/tours";

        $response = $this->get("{$endpoint}?priceFrom=100");
        $response->assertJsonCount(2, "data");
        $response->assertJsonFragment(["id" => $cheapTour->id]);
        $response->assertJsonFragment(["id" => $expensiveTour->id]);

        $response = $this->get("{$endpoint}?priceFrom=150");
        $response->assertJsonCount(1, "data");
        $response->assertJsonMissing(["id" => $cheapTour->id]);
        $response->assertJsonFragment(["id" => $expensiveTour->id]);

        $response = $this->get("{$endpoint}?priceTo=200");
        $response->assertJsonCount(2, "data");
        $response->assertJsonFragment(["id" => $cheapTour->id]);
        $response->assertJsonFragment(["id" => $expensiveTour->id]);

        $response = $this->get("{$endpoint}?priceTo=150");
        $response->assertJsonCount(1, "data");
        $response->assertJsonMissing(["id" => $expensiveTour->id]);
        $response->assertJsonFragment(["id" => $cheapTour->id]);

        $response = $this->get("{$endpoint}?priceTo=50");
        $response->assertJsonCount(0, "data");
        $response->assertJsonMissing(["id" => $expensiveTour->id]);
        $response->assertJsonMissing(["id" => $cheapTour->id]);

        $response = $this->get("{$endpoint}?priceFrom=150&priceTo=250");
        $response->assertJsonCount(1, "data");
        $response->assertJsonMissing(["id" => $cheapTour->id]);
        $response->assertJsonFragment(["id" => $expensiveTour->id]);
    }

    public function test_tours_list_filters_by_starting_date_correctly(): void
    {
        $travel = Travel::factory()->create();
        $futureTour = Tour::factory()->create([
            "travel_id" => $travel->id,
            "starting_date" => today()->addDays(2),
            "ending_date" => now()->addDays(3),
        ]);
        $earlierTour = Tour::factory()->create([
            "travel_id" => $travel->id,
            "starting_date" => today(),
            "ending_date" => today()->addDays(2),
        ]);

        $endpoint = "/api/v1/travels/{$travel->slug}/tours";

        $response = $this->get("{$endpoint}?dateFrom=". today());
        $response->assertJsonCount(2, "data");
        $response->assertJsonFragment(["id" => $futureTour->id]);
        $response->assertJsonFragment(["id" => $earlierTour->id]);

        $response = $this->get("{$endpoint}?dateFrom=" . today()->addDay());
        $response->assertJsonCount(1, "data");
        $response->assertJsonFragment(["id" => $futureTour->id]);
        $response->assertJsonMissing(["id" => $earlierTour->id]);

        $response = $this->get("{$endpoint}?dateFrom=" . today()->addDays(5));
        $response->assertJsonCount(0, "data");

        $response = $this->get("{$endpoint}?dateTo=" . today()->addDays(5));
        $response->assertJsonCount(2, "data");

        $response = $this->get("{$endpoint}?dateTo=" . today()->subDay());
        $response->assertJsonCount(0, "data");

        $response = $this->get("{$endpoint}?dateFrom=".today()->addDay()."&dateTo=" . today()->addDays(5));
        $response->assertJsonCount(1, "data");
        $response->assertJsonFragment(["id" => $futureTour->id]);
        $response->assertJsonMissing(["id" => $earlierTour->id]);
    }


    // Finally test validation is working as expected
    public function test_tour_list_validation_errors( ): void
    {
        $travel = Travel::factory()->create();

        $response = $this->get("/api/v1/travels/{$travel->slug}/tours?dateFrom=notadate");
        $response->assertStatus(422);

        $response = $this->get("/api/v1/travels/{$travel->slug}/tours?priceFrom=notnumber");
        $response->assertStatus(422);
        $response->assertStatus(422);

        $response = $this->get("/api/v1/travels/{$travel->slug}/tours?sortOrder=random");
        $response->assertStatus(422);
    }
}

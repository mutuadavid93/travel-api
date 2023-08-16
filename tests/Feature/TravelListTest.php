<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Travel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


/*
|--------------------------------------------------------------------------
| Tests
|--------------------------------------------------------------------------
|
| - All tests regarding a feature here.
| - NOTE: Always immediately after writing a feature write the corresponding test 
| when the feature is fresh in memory
*/

class TravelListTest extends TestCase
{
    // Make sure runs migrate fresh command everytime before tests can run.
    // TIP: uncomment connection here - phpunit.xml
    use RefreshDatabase;

    public function test_travel_list_returns_paginated_data_correctly(): void
    {
        // Fake some data using factory
        // NOTE: pagination is 15 records per page
        // Pass fields which need overriding inside create(). Making all records public 
        // thus returning all 16. Recall all travels are public.
        Travel::factory()->count(16)->create(["is_public" => true]);

        // Input: the travel list payload
        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);

        // List contains 15 records i.e. default per page
        $response->assertJsonCount(15, "data");

        // 2 because first page is 15 records but since they are 16, 1 record overflows to next page
        $response->assertJsonPath("meta.last_page", 2);
    }


    public function test_travel_list_shows_only_public_records(): void
    {
        $publicTravel = Travel::factory()->create(["is_public" => true]);
        Travel::factory()->create(["is_public" => false]);

        // Input: the travel list payload
        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);

        // only the public record
        $response->assertJsonCount(1, "data");

        // Assert individal record's properties
        $response->assertJsonPath("data.0.name", $publicTravel->name);
    }
}

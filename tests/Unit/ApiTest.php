<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Faker\Generator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use WithFaker;

    public function testApiAccess()
    {
        $response = $this->get('/api/weather');
        $response->assertDontSee('message');
        $response->assertDontSee('error');
        $response->assertStatus(200);
    }

    public function testSpecificApiEndpoints()
    {
        $response = $this->get('/api/weather/locations?date=1985-01-01&lon=-86.69&lat=36.12');
        $response->assertDontSee('error');
        $response->assertStatus(200);
    }

    public function testAddWeatherData()
    {
        $faker = $this->faker;
        $data = [
            "date" => Carbon::now()->subDays(rand(1, 100))->format('Y-m-d'),
            "location" => [
                "lat" => $faker->latitude,
                "lon" => $faker->longitude,
                "city" => $faker->city,
                "state" => $faker->city
            ],
            "temperature" => $faker->shuffleArray([
                17.3, 36.8, 36.4, 36.0, 35.6, 35.3,
                35.0, 34.9, 35.8, 38.0, 40.2, 42.3,
                43.8, 44.9, 45.5, 45.7, 44.9, 43.0,
                41.7, 40.8, 39.9, 39.2, 38.6, 68.1
            ])
        ];

        $response = $this->post('/api/weather/', $data);
        if ($response->getStatusCode() === 201) {
            $response->assertStatus(201);
        }
        else {
            $response->assertStatus(401);
            $response->assertSee('Validation');
        }
    }
}

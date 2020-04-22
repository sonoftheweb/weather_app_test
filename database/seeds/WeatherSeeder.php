<?php

use App\Models\Weather;
use App\Models\WeatherLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class WeatherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get(base_path("database/data/weather.json"));
        $weathers = json_decode($json, true);

        foreach ($weathers as $weatherdata) {
            $location = WeatherLocation::firstOrCreate([
                'lat' => $weatherdata['location']['lat'],
                'lon' => $weatherdata['location']['lon'],
                'city' => $weatherdata['location']['city'],
                'state' => $weatherdata['location']['state']
            ]);

            $weather = $location->weathers()->create([
                'id' => $weatherdata['id'],
                'date' => $weatherdata['date']
            ]);

            $temps  = array_map(function ($temp) use ($weather) {
                return [
                    'weather_id' => $weather->id,
                    'temperature' => $temp
                ];
            }, $weatherdata['temperature']);
            $location->temperatures()->createMany($temps);
        }
    }
}

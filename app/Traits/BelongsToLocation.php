<?php


namespace App\Traits;


use App\Models\WeatherLocation;

trait BelongsToLocation
{
    /**
     * Relationships between using model and the location
     * Define once, use anywhere
     *
     * @return mixed
     */
    public function location()
    {
        return $this->belongsTo(WeatherLocation::class, 'location_id');
    }
}

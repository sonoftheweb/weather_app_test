<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeatherLocation extends Model
{

    protected $table = 'weather_locations';
    protected $fillable = [
        'weather_id',
        'lat',
        'lon',
        'city',
        'state'
    ];

    /**
     * Weathers for this location
     *
     * @return HasMany
     */
    public function weathers()
    {
        return $this->hasMany(Weather::class, 'location_id');
    }

    /**
     * Temperatures for this location
     *
     * @return HasMany
     */
    public function temperatures()
    {
        return $this->hasMany(WeatherTemperature::class, 'location_id');
    }
}

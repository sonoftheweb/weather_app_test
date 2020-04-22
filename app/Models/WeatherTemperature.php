<?php

namespace App\Models;

use App\Traits\BelongsToLocation;
use Illuminate\Database\Eloquent\Model;

class WeatherTemperature extends Model
{
    // Using this trait to reduce the amount of code written.
    // Yes I could have added a class by which I can extend from, but I am not sure that would be a good idea
    // seeing the nature of all the relationships. It would force me to instantiate model calls manually so no one instance
    // will be mistaken for another.
    use BelongsToLocation;

    protected $table = 'weathers_temperatures';
    protected $fillable = [
        'weather_id',
        'temperature',
        'location_id'
    ];

    function weather()
    {
        return $this->belongsTo(\App\Models\Weather::class, 'weather_id');
    }
}

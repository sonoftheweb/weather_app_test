<?php

/**
 * Created by Reliese Model.
 * Date: Wed, 03 Jul 2019 21:07:39 +0000.
 */

namespace App\Models;

use App\Traits\BelongsToLocation;
use Illuminate\Support\Facades\DB;
use Reliese\Database\Eloquent\Model as Eloquent;

/**
 * Class WeatherResource
 *
 * @property int $id
 * @property \Carbon\Carbon $date
 * @property array $location
 * @property array $temperature
 *
 * @package App\Models
 */
class Weather extends Eloquent
{
    use BelongsToLocation; // See WeatherTemperature.php (in Models) for why...

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'date',
		'location',
		'temperature'
	];

    /**
     * Relations between Weather data and all temperatures for said weather
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function temperatures()
    {
        return $this->hasMany(WeatherTemperature::class, 'weather_id');
    }
}

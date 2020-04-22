<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeatherLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array, giving us the ability to perform some other operations if needed
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'lat' => (float) $this->lat,
            'lon' => (float) $this->lon,
            'city' => $this->city,
            'state' => $this->state,
            'distance' => (int) $this->distance,
            'median_temperature' => (float) round($this->median_temperature, 1)
        ];
    }
}

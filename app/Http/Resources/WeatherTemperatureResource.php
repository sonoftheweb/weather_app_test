<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WeatherTemperatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'lat' => $this->location->lat,
            'lon' => $this->location->lon,
            'city' => $this->location->city,
            'state' => $this->location->state,
            'lowest' => $this->temperatures->min('temperature'),
            'highest' => $this->temperatures->max('temperature'),
        ];
    }
}

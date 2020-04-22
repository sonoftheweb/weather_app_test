<?php

namespace App\Http\Resources;

use App\Models\Weather;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WeatherTemperatureCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->collection->transform(function (Weather $weather) {
            return new WeatherTemperatureResource($weather);
        });
        return parent::toArray($request);
    }
}

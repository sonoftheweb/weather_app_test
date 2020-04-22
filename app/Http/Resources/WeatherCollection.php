<?php

namespace App\Http\Resources;

use App\Models\Weather;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WeatherCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->collection->transform(function (Weather $weather) {
            return new WeatherResource($weather);
        });
        return parent::toArray($request);
    }
}

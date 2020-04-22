<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WeatherLocationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // receive the data (collection) and pass it on to a resource
        $this->collection->transform(function ($weatherLocation) {
            return new WeatherLocationResource($weatherLocation);
        });
        return parent::toArray($request);
    }
}

<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class WeatherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $temps = ($this->temperatures) ? $this->temperatures->toArray() : [];
        return [
            'id' => $this->id,
            'date' => Carbon::parse($this->date)->format('Y-m-d'),
            'location' => [
                'lat' => (float) $this->location->lat,
                'lon' => (float) $this->location->lon,
                'city' => $this->location->city,
                'state' => $this->location->state
            ],
            'temperature' => array_map(function ($temp) {
                return $temp['temperature'];
            }, $temps)
        ];
    }
}

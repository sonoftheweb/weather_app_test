<?php

namespace App\Http\Controllers;

use App\Helpers\UtilsHelper;
use App\Http\Resources\WeatherCollection;
use App\Http\Resources\WeatherLocationCollection;
use App\Http\Resources\WeatherTemperatureCollection;
use App\Models\Weather;
use App\Models\WeatherLocation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function foo\func;

class WeatherController extends BaseController
{
    /**
     * Erase some (by dates) or all weather data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function erase(Request $request)
    {
        $weathers = Weather::query();

        if (
            $request->has('start') &&
            $request->has('end') &&
            $request->has('lat') &&
            $request->has('lon')
        ) {
            $weathers = $weathers->where('date', '>=', $request->start)
                ->where('date', '<=', $request->end)
                ->whereHas('location', function (Builder $query) use ($request) {
                    $query->where('lat', $request->lat)->where('lon', $request->lon);
                });
        }
        $weathers->delete();

        return $this->respond('Erase successful');
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $weather = Weather::with('location', 'temperatures');

        if ($request->has('lon') && $request->has('lat')) {
            $weather = $weather->whereHas('location', function (Builder $query) use ($request) {
                $query->where('lon', $request->lon)
                    ->where('lat', $request->lat);
            });
        }

        if ($request->has('start') && $request->has('end')) {
            $weather = $weather->where('date', '>=', $request->start)
                ->where('date', '<=', $request->end);
        }

        $response = new WeatherCollection($weather->orderBy('id')->get());
        return $this->respond($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'bail|required|unique:weathers,id|numeric',
                'date' => 'bail|required|date',
                'location.lat' => 'bail|required|numeric',
                'location.lon' => 'bail|required|numeric',
                'location.city' => 'bail|required|max:100',
                'location.state' => 'bail|required|max:100',
                'temperature' => 'bail|required'
            ]);

            if ($validator->fails()) {
                $this->setStatusCode(400);

                return $this->respond([
                    'status' => 'warning',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ]);
            }

            // create location and weather
            $location = WeatherLocation::firstOrCreate($request->location);
            $weather = $location->weathers()->create($request->only('id', 'date'));

            $temps  = array_map(function ($temp) use ($weather) {
                return [
                    'weather_id' => $weather->id,
                    'temperature' => $temp
                ];
            }, $request->temperature);

            $location->temperatures()->createMany($temps);

            $this->setStatusCode(201);
            return $this->respond([
                'status' => 'success',
                'message' => 'Successfully added weather.'
            ]);
        } catch (\Exception $e) {
            return $this->respondWithErrorMessage($e);
        }
    }

    /**
     * Update the weather data selected.
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'location.lat' => 'required|numeric',
                'location.lon' => 'required|numeric',
                'location.city' => 'required|max:100',
                'location.state' => 'required|max:100',
                'temperature' => 'required'
            ]);

            if ($validator->fails()) {
                $this->setStatusCode(400);

                return $this->respond([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ]);
            }

            // check if location exist already and if any change is made to location
            $location = WeatherLocation::updateOrCreate(
                [
                    'lat' => $request->location['lat'],
                    'lon' => $request->location['lon']
                ],
                [
                    'city' => $request->location['city'],
                    'state' => $request->location['state']
                ]
            );

            // update weather
            $weather = Weather::where('id', $id)->with('location')->first();
            $weather->date = $request->date;
            $weather->location_id = $location->id;
            $weather->save();

            // remove all temps so we may create new ones
            $weather->temperatures()->delete();

            $temps  = array_map(function ($temp) use ($location) {
                return [
                    'location_id' => $location->id,
                    'temperature' => $temp
                ];
            }, $request->temperature);

            $weather->temperatures()->createMany($temps);

            $this->setStatusCode(201);
            return $this->respond('Successfully added weather.');
        } catch (\Exception $e) {
            return $this->respondWithError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            Weather::destroy($id);
            return $this->respond('Deleted record');
        } catch (\Exception $e) {
            return $this->respondWithError($e);
        }
    }

    /**
     * Get items between two methods location and temperature
     *
     * @param Request $request
     * @param $locOrTemp string
     * @return \Illuminate\Http\JsonResponse
     */
    public function locOrTemp(Request $request, $locOrTemp)
    {
        if (!method_exists($this, $locOrTemp)) {
            $this->setStatusCode(404);
            return $this->respond('Method does not exist');
        }

        return $this->$locOrTemp($request);
    }

    /**
     * Get the temperature data for locations, dynamically called by locOrTemp()
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function temperature(Request $request)
    {
        $locations = WeatherLocation::with('temperatures')
            ->whereHas('weathers', function (Builder $query) use ($request){
            $query->where('date', '>=', $request->start)
                ->where('date', '<=', $request->end);
        })->get();

        $data = [];
        foreach ($locations as $location) {
            $temp = $location->temperatures->toArray();
            $temp = collect($temp);

            $data[] = [
                'lat' => $location->lat,
                'lon' => $location->lon,
                'city' => $location->city,
                'state' => $location->state,
                'lowest' => $temp->min('temperature'),
                'highest' => $temp->max('temperature'),
            ];
        }

        return $this->respond($data);
    }

    /**
     * Get the preferred locations data based on criteria described, dynamically called by locOrTemp()
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    private function locations(Request $request)
    {
        try {
            // get the last date of the next 72 hours, just add three days.
            $next72Hours = Carbon::parse($request->date)->addDays(3)->format('Y-m-d');

            // data for the given weather. This is directly from the query.
            $given = Weather::with('location')->where('date', $request->date)
                ->whereHas('location', function (Builder $query) use ($request) {
                    $query->where('lon', $request->lon)->where('lat', $request->lat);
                })->with('location')
                ->first();

            // if no given data, fall back with a message.
            if (!$given) {
                return $this->respond(['message' => 'Given weather data not found']);
            }

            // Sum of temperatures for given day. I'll use this to figure out the difference required
            $totalTemperatureOfGivenWeatherData = $given->temperatures()->sum('temperature');

            // on each hour, difference should not be more than 20 degrees so total temp max should be
            $maximumDifference = 24 * 20; // difference in sum of temps should not be more than this (24 being the 24 hours)

            // get the weather locations without the given data AND in between the given date and the next 72 hours (72nd hour included)
            $otherLocations = WeatherLocation::select(
                'lat',
                'lon',
                'city',
                'state',
                DB::raw('ROUND(haversine(lat, lon, ' . $given->location->lat . ', '. $given->location->lon .')) as distance'), // make sure you have the haversine mysql function already added, just to make things cleaner
                DB::raw('SUM(weathers_temperatures.temperature) / COUNT(weathers_temperatures.temperature) as median_temperature') // median temperature
            )
            ->leftJoin('weathers_temperatures', 'weathers_temperatures.location_id', '=', 'weather_locations.id')
            ->whereHas('weathers', function (Builder $query) use ($request, $next72Hours) { // get only between dates from the next day after given to the end of 72 hours.
                $query->where('date', '>', $request->date)
                    ->where('date', '<=', $next72Hours);
            })
            ->whereHas('temperatures', function (Builder $query) use ($totalTemperatureOfGivenWeatherData, $maximumDifference) { // get only if the difference is no more than x and no less than -x (absolute of -x is x)
                // for some reason ABS fails for me so I manually check for the absolute difference
                $query->groupBy(['id'])
                    ->havingRaw('SUM(temperature) -  ? <= ?', [$totalTemperatureOfGivenWeatherData, $maximumDifference])
                    ->havingRaw('? - SUM(temperature)   >= ?', [$totalTemperatureOfGivenWeatherData, $maximumDifference]);
            })
            ->where('state', '!=', $given->location->state)
            ->groupBy('weather_locations.id')
            ->orderBy('distance')
            ->orderBy('median_temperature')
            ->orderBy('city')
            ->orderBy('state')
            ->get();

            // not required but it would be nice to have a fall back here
            if ($otherLocations->count() === 0) {
                return $this->respond(['message' => 'Nothing found']);
            }

            // I am passing this into the api resource so I may perform operations like typecasting, will come in useful
            return $this->respond(new WeatherLocationCollection($otherLocations));
        } catch (\Exception $e) {
            return $this->respondWithError($e);
        }
    }
}

<?php

namespace App\Adapters;

use Illuminate\Database\Eloquent\Collection;
use App\Interfaces\Forecast;
use App\Weather;
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;
use Illuminate\Support\Facades\Cache;

class OpenweatherForecast implements Forecast {

    const LANGUAGE = 'en';
    const UNIT = 'imperial'; // metric or imperial

    /**
     * Get forecast by city
     *
     * @param $city
     * @return Collection
     * @throws \Exception
     */
    public function get($city) : Collection
    {
        $cache_key = 'forecast_openweather_' . $city;
        $forecast = Cache::get($cache_key);

        if (!$forecast)
        {
            $weather_api = new OpenWeatherMap(env('OPENWEATHERMAP_API_KEY'));
            $forecast = new Collection;
            try {

                $response = $weather_api->getWeatherForecast($city, self::UNIT, self::LANGUAGE, '', 5);
                foreach($response as $weather) {

                    // Openweather have the forecasts in 3 hours periods
                    // So we are only considering 1st period per day, instead of averages or something like lowest low or highest highs
                    // for the sake of simplicity of this test
                    $weather = $this->parse($weather);
                    if($forecast->where('date', $weather->date)->count() == 0)
                    {
                        $forecast->push($weather);
                    }
                }
                Cache::put($cache_key, $forecast, 60);
                return $forecast;
            } catch(OWMException $e) {

                return new Collection;
            } catch(\Exception $e) {

                return new Collection;
            }
        } else {
            return $forecast;
        }
    }

    /*
     * Parse the weather object from API response
     *
     * @param $weather
     * @return Weather
     */
    private function parse($weather) : Weather {
        return new Weather([
            'date' => date_format($weather->time->from, 'l'),
            'min' => $weather->temperature->min->getValue(),
            'max' => $weather->temperature->max->getValue(),
            'now' => $weather->temperature->now->getValue(),
        ]);
    }
}

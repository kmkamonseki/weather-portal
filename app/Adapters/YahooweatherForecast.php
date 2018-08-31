<?php

namespace App\Adapters;

use App\Interfaces\Forecast;
use App\Weather;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Th3Mouk\YahooWeatherAPI\YahooWeatherAPI;


class YahooweatherForecast implements Forecast {

    const UNIT = 'f';
    const DAYS = 5;

    /**
     * Get forecast by city
     *
     * @param $city
     * @return Collection
     * @throws \Exception
     */
    public function get($city) : Collection
    {
        $cache_key = 'forecast_yahoo_' . $city;
        $forecast = Cache::get($cache_key);

        if (!$forecast)
        {
            $forecast = new Collection;
            $yahooWeather = new YahooWeatherAPI();
            $response = $yahooWeather->callApiCityName($city, self::UNIT);

            if ($response) {
                $count = 0;
                foreach ($response["item"]["forecast"] as $weather) {
                    if ($count < self::DAYS)
                        $forecast->push($this->parse($weather));
                    $count++;
                }
            }
            Cache::put($cache_key, $forecast, 60);
        }

        return $forecast;
    }

    /**
     * Parse API response weather object to Weather Model
     *
     * @param $weather
     * @return Weather
     */
    private function parse($weather) : Weather
    {
        return new Weather([
            'date' => $weather['day'],
            'min' => $weather['low'],
            'max' => $weather['high'],
            'now' => (($weather['low'] + $weather['high']) / 2) // API does not have current temperature
        ]);
    }
}

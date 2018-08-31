<?php

namespace App\Factories;

use App\Interfaces\Forecast;

class ForecastFactory
{
    /*
     * @param $source
     * @return Forecast
     */
    public function make($source) : Forecast {
        switch($source) {
            case 'openweathermap':
                return new \App\Adapters\OpenweatherForecast;
            case 'yahooweather':
                return new \App\Adapters\YahooweatherForecast;
            default:
                throw new \RuntimeException('Unknown Forecast service');
        }
    }
}

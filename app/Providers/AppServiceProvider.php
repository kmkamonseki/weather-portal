<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Interfaces\Forecast::class, function($app) {
            $factory = new \App\Factories\ForecastFactory;
            return $factory->make($app->make('config')->get('services.forecast-api'));
        });
    }
}

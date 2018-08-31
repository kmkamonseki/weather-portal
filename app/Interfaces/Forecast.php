<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface Forecast {

    /*
     * Get forecast by city
     *
     * @param $city
     * @return Collection
     */
    public function get($city) : Collection;

}

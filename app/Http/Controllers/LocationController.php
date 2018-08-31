<?php

namespace App\Http\Controllers;

use App\{Location, Interfaces\Forecast};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Return all locations
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function index()
    {
        return response(Auth::user()->locations->jsonSerialize(), Response::HTTP_OK);
    }

    /**
     * Save the location if the weather service can pull the information
     * If location already exists, just associate it to the current user,
     * avoiding extra API calls to validate the location
     *
     * @param Request $request
     * @param Forecast $forecast
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function store(Request $request, Forecast $forecast)
    {
        $city = ucwords($request->input('city'));
        $location = Location::where('city', $city)->first();

        // location already exists, created by this or other user
        if($location) {

            $user = $location->users()->where('user_id', Auth::user()->id)->first();
            if(!$user) $location->users()->attach(Auth::user());
            return response($location->jsonSerialize(), Response::HTTP_CREATED);

        } else {

            // we validate checking the forecast api first
            $forecast = $forecast->get($city);
            if($forecast->count() > 0) {
                $location = new Location([
                    'city' => $city
                ]);
                $location->save();
                $location->users()->attach(Auth::user());

                return response($location->jsonSerialize(), Response::HTTP_CREATED);
            }
            else {
                return response('', Response::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Delete the location associated to the current user
     * The actual location will continue to exist, just the relationship between user and location is removed
     *
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function destroy($id)
    {
        Auth::user()->locations()->detach($id);

        return response(null, Response::HTTP_OK);
    }

    /**
     * Get weather forecast by location
     *
     * @param $id
     * @param Forecast $forecast
     * @return \Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function forecast($id, Forecast $forecast){
        $location = Location::find($id);
        return response($forecast->get($location->city), Response::HTTP_OK );
    }
};

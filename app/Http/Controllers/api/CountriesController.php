<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountriesRequest;
use App\Models\Country;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    public function list()
    {
    $rules = [];
    ValidateApiRequest($rules, request()->all());
        try {
            $countries = Country::with(['states.counties'])->get();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $countries);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}

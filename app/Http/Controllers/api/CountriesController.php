<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountriesRequest;
use App\Models\Country;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    public function list(Request $request)
    {
    $rules = [];
    ValidateApiRequest($rules, request()->all());
        try {
            // Callers that only need country names/ids (e.g. the homepage search
            // widget) can skip the nested states/counties tree, which is ~550KB
            // across all countries and was slowing down that initial dropdown load.
            if ($request->boolean('light')) {
                $countries = Country::select('id', 'name')->get();
            } else {
                $countries = Country::with(['states.counties'])->get();
            }
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $countries);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}

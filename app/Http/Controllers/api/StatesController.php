<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StateRequest;
use Illuminate\Http\Request;
use App\Models\State;

class StatesController extends Controller
{
    public function list(Request $request)
    {
        $rules=  [
                'country_id' => 'required',
        ];
        ValidateApiRequest($rules, $request->all());
        try {
            $states = State::where('country_id',$request->country_id)->get();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $states);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

use App\Models\County;
use App\Models\Role;
use Illuminate\Http\Request;

class CountiesController extends Controller
{
 public function list(Request $request)
    {
        $rules=  [
                'state_id' => 'required',
        ];
        ValidateApiRequest($rules, $request->all());
        try {
            $counties = County::where('state_id',$request->state_id)->get();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $counties);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

public function roleList(){
    try {
             $roles = Role::where('name', '!=', 'Admin')->with('badge')->get();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $roles);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
}
}

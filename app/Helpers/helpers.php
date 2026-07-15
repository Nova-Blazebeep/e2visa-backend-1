<?php

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

function makeResponse($code, $message, $result=null, $errors=null)
{
    return response()->json([
        'server_maintenance' => env('SERVER_MAINTENANCE'),
        'app_update' => env('APP_UPDATE'),
        'message' => $message,
        'errors' => $errors,
        'result' => $result,
    ], $code);
}



function makeRedirectResponse($code,$message,$route)
{
    return response()->json([
        'message' => $message,
        'redirectURL' => $route, 
    ], $code);
}

    function ValidateApiRequest(array $rules, array $data)
    {
        $request = app(ApiRequest::class);
        $request->setRules($rules);
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            throw new HttpResponseException(makeResponse(FAILURE_CODE, 'validation error', false, $validator->errors()));
        }
        return true;
    }

   if (!function_exists('getUserByRole')) {
        function getUserByRole($role)
        {
            return User::where('role',$role)->get(); 
        }
    }


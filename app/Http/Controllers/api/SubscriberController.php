<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriberReuqest;
use App\Mail\SubscriberConfirmationMail;
use App\Models\Subscriber;
use Error;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;


class SubscriberController extends Controller
{
    public function store(SubscriberReuqest $request)
    {
        try {
            $validated = $request->validated();
            $existing=Subscriber::where('email',$request->email)->first();
            if($existing){
                return makeResponse(FAILURE_CODE,'You are already subscribed');
            }
            $subscriber = Subscriber::create($validated);

            $token = request()->bearerToken();
            $user = null;

            if ($token) {
                $accessToken = PersonalAccessToken::findToken($token);
                if ($accessToken) {
                    $user = $accessToken->tokenable;
                }
            }

            $nameOrEmail = ($user && !empty($user->name)) ? $user->name : $subscriber->email;

            Mail::to($subscriber->email)->send(new SubscriberConfirmationMail($subscriber, $nameOrEmail));

            return makeResponse(SUCCESS_CODE, "Thanks! You've successfully subscribed to our newsletter.", $subscriber);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}

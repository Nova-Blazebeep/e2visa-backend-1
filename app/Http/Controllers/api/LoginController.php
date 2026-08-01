<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
   public function login(LoginRequest $request)
{

    $credentials = $request->only('email', 'password');
    $remember = $request->has('remember'); 
    if (Auth::attempt($credentials, $remember)) {
        $user = Auth::user();
        if ($user->email_verified_at) {
            
            $token = $user->createToken('authToken')->plainTextToken;
            $user_info=UserInformation::where('user_id',$user->id)->first();
            $user['token']=$token;
            $user['phone']=$user_info->phone_number??'NA';
            // null for Buyer/Admin/Moderator (no badge required at all); otherwise
            // the actual pending/active/revoked status so the frontend can gate
            // forum posting and show the right Settings-page messaging.
            $user['badge_status'] = in_array($user->role, ['Buyer', 'Admin', 'Moderator'], true)
                ? null
                : ($user->userBadge->status ?? null);
            // The role-specific icon (Business Broker, Attorney, etc.) — only
            // populated once this user's badge is actually active.
            $user['role_badge_icon'] = $user->activeBadgeIcon();

            return makeResponse(SUCCESS_CODE, AUTH_SUCCESS, $user);
        } else {
            Auth::logout();
            return makeResponse(ABORT_CODE, 'Please Verify Email First');
        }
    }
    return makeResponse(UNAUTHNTICATED_CODE, AUTH_FAILED);
}
}

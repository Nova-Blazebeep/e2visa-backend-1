<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserInformation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function sendResetPasswordEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No user found with this email address.'
                ], 404);
            }

            $length = rand(8, 12);
            $newPassword = Str::random($length);

            $user->password = Hash::make($newPassword);
            $user->save();

            Mail::send('emails.new_password', ['user' => $user, 'password' => $newPassword], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your New Password');
            });

            return makeResponse(SUCCESS_CODE, 'Password reset email sent successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);


            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return makeResponse(FAILURE_CODE, 'Current password is incorrect.');
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return makeResponse(SUCCESS_CODE, 'Password changed successfully!');
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateProfileImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $user = Auth::user();

            if (!$user) {
                return makeResponse(FAILURE_CODE, 'Unauthorized access.');
            }

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('user_images', 'public');
                $imageSavePath = str_replace('public/', '', $imagePath);

                $user->update([
                    'image' => $imageSavePath,
                ]);

                UserInformation::updateOrCreate(
                    ['user_id' => $user->id],
                    ['image' => $imageSavePath]
                );

                return makeResponse(SUCCESS_CODE, 'Profile image updated successfully.', [
                    'image_url' => $imageSavePath
                ]);
            }

            return makeResponse(FAILURE_CODE, 'No image was uploaded.');
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, 'An error occurred: ' . $e->getMessage());
        }
    }
}

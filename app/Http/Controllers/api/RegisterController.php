<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\PasswordResetTokens;
use App\Models\State;
use App\Models\User;
use App\Models\UserInformation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    // public function register(Request $request)
    // {

    //     $rules= [
    //         'name'     => 'required|string|max:255',
    //         'email'    => 'required|email',
    //         'password' => 'required|min:6|confirmed',
    //         'role_id'     => 'required|string',
    //         // user_information table fields
    //         'phone_number'              => 'required|numeric',
    //         'time_frame_for_immigration' => 'nullable|string|max:255',
    //         'address'                   => 'nullable|string|max:255',
    //         'country_id'                   => 'required|string|max:100',
    //         'city'                      => 'nullable|string|max:100',
    //         'state_id'                     =>'required|string|max:100',
    //         'zipcode'                   => 'required|string|max:20',
    //         'subscribe_for_newsletter'     => 'required|boolean',
    //         'have_broker'              => 'nullable|boolean',
    //         'have_attorney'            => 'nullable|boolean',
    //         'broker_license'           => 'nullable|string|max:255',
    //         'attorney_license'         => 'nullable|string|max:255',
    //     ];
    //     ValidateApiRequest($rules,$request->all());

    //     try {
    //         $existingUser = User::where('email', $request->email)->first();
    //         $role=Role::findorfail($request->role_id);
    //         $country=Country::findorfail($request->country_id);

    //         $state=State::findorfail($request->state_id);
    //         if($existingUser->updated_at >= Carbon::now()->subMinutes(10)){
    //              return makeResponse(NOT_ACCEPTABLE, 'Please Wait at least 10 minutes before re attempt');
    //         }
    //         else if ($existingUser) {
    //             if ($existingUser->email_verified_at) {
    //                 return makeResponse(NOT_ACCEPTABLE, 'Email already registered and verified. Please login.');
    //             } else {
    //                 $existingUser->update([
    //                     'name'     => $request->name,
    //                     'password' => Hash::make($request->password),
    //                     'role_id'     => $role->id,
    //                     'role'     => $role->name,
    //                 ]);

    //                 UserInformation::updateOrCreate(
    //                     ['user_id' => $existingUser->id],
    //                     [
    //                         'phone_number'           => $request->phone_number,
    //                         'time_frame_for_immigration' => $request->time_frame_for_immigration,
    //                         'address'                => $request->address,
    //                         'country_name'                => $country->name,
    //                         'country_id'                => $country->id,
    //                         'city'                   => $request->city,
    //                         'state_name'                  => $state->name,
    //                         'state_id'                  => $state->id,
    //                         'zipcode'                => $request->zipcode,
    //                         'register_news_letter'  => $request->register_news_letter,
    //                         'have_broker'            => $request->have_broker,
    //                         'have_attorney'          => $request->have_attorney,
    //                         'subscribe_for_newsletter' => $request->subscribe_for_newsletter,
    //                         'broker_license'         => $request->broker_license,
    //                         'attorney_license'       => $request->attorney_license,
    //                     ]
    //                 );

    //                 $token = Str::random(60);
    //                 PasswordResetTokens::updateOrInsert(
    //                     ['email' => $existingUser->email],
    //                     ['token' => $token, 'created_at' => Carbon::now()]
    //                 );

    //                 $verificationUrl = url("/api/verify-email/{$token}");

    //                 Mail::send('emails.verify-email', [
    //                     'url' => $verificationUrl,
    //                     'name' => $existingUser->name
    //                 ], function ($message) use ($existingUser) {
    //                     $message->to($existingUser->email)
    //                         ->subject('Verify your email address');
    //                 });

    //                 return makeResponse(SUCCESS_CODE, 'Your previous unverified account was updated. Please check your email to verify.');
    //             }
    //         }

    //         $user = User::create([
    //             'name'     => $request->name,
    //             'email'    => $request->email,
    //             'password' => Hash::make($request->password),
    //             'role_id'     => $role->id,
    //             'role'     => $role->name,
    //         ]);

    //         $token = Str::random(60);

    //         PasswordResetTokens::updateOrInsert(
    //             ['email' => $user->email],
    //             ['token' => $token, 'created_at' => Carbon::now()]
    //         );

    //         $verificationUrl = url("/api/verify-email/{$token}");

    //         Mail::send('emails.verify-email', [
    //             'url' => $verificationUrl,
    //             'name' => $user->name
    //         ], function ($message) use ($user) {
    //             $message->to($user->email)
    //                 ->subject('Verify your email address');
    //         });

    //         UserInformation::create([
    //                         'user_id'=>$user->id,
    //                         'phone_number'           => $request->phone_number,
    //                         'time_frame_for_immigration' => $request->time_frame_for_immigration,
    //                         'address'                => $request->address,
    //                         'country_name'                => $country->name,
    //                         'country_id'                => $country->id,
    //                         'city'                   => $request->city,
    //                         'state_name'                  => $state->name,
    //                         'state_id'                  => $state->id,
    //                         'zipcode'                => $request->zipcode,
    //                         'register_news_letter'  => $request->register_news_letter,
    //                         'have_broker'            => $request->have_broker,
    //                         'have_attorney'          => $request->have_attorney,
    //                         'subscribe_for_newsletter' => $request->subscribe_for_newsletter,
    //                         'broker_license'         => $request->broker_license,
    //                         'attorney_license'       => $request->attorney_license,
    //         ]);

    //         return makeResponse(SUCCESS_CODE, 'Registration successful. Please check your email to verify your account.');
    //     } catch (\Exception $e) {
    //         return makeResponse(FAILURE_CODE, $e->getMessage());
    //     }
    // }



    public function register(Request $request)
{
    $rules = [
        'name'     => 'required|string|max:255',
        'email'    => ['required', 'email', Rule::unique('users')->whereNull('deleted_at')],
        'password' => 'required|min:6|confirmed',
        'role_id'     => 'required|string',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // user_information table fields
        'phone_number'              => 'required|string|max:30',
        'time_frame_for_immigration' => 'nullable|string|max:255',
        'address'                   => 'nullable|string|max:255',
        'country_id'                   => 'required|string|max:100',
        'city'                      => 'nullable|string|max:100',
        'state_id'                     => 'required|string|max:100',
        'zipcode'                   => 'required|string|max:20',
        'subscribe_for_newsletter'     => 'required|boolean',
    ];

    ValidateApiRequest($rules, $request->all());

    try {
        $response = DB::transaction(function () use ($request) {
            $existingUser = User::where('email', $request->email)->first();
            $role = Role::findOrFail($request->role_id);
            $country = Country::findOrFail($request->country_id);
            $state = State::findOrFail($request->state_id);

            // $imagePath = null;
            // if ($request->hasFile('image')) {
            //     $imagePath = $request->file('image')->store('user_images', 'public');
            //     $imageSavePath = str_replace('public/', '', $imagePath);
            // }


            if ($existingUser && $existingUser->updated_at >= Carbon::now()->subMinutes(10)) {
                return makeResponse(NOT_ACCEPTABLE, 'Please wait at least 10 minutes before reattempt.');
            }

            if ($existingUser) {
                if ($existingUser->email_verified_at) {
                    return makeResponse(NOT_ACCEPTABLE, 'Email already registered and verified. Please log in.');
                }

                $existingUser->update([
                    'name'     => $request->name,
                    'password' => Hash::make($request->password),
                    'role_id'  => $role->id,
                    'role'     => $role->name,
                ]);
                $existingUser->syncRoles($role->name);

                UserInformation::updateOrCreate(
                    ['user_id' => $existingUser->id],
                    [
                        'phone_number'           => $request->phone_number,
                        'time_frame_for_immigration' => $request->time_frame_for_immigration,
                        'address'                => $request->address,
                        'country_name'           => $country->name,
                        'country_id'             => $country->id,
                        'city'                   => $request->city,
                        'state_name'             => $state->name,
                        'state_id'               => $state->id,
                        'zipcode'                => $request->zipcode,
                        'register_news_letter'   => $request->register_news_letter,
                        'subscribe_for_newsletter' => $request->subscribe_for_newsletter,
                    ]
                );

                $token = Str::random(60);
                PasswordResetTokens::updateOrInsert(
                    ['email' => $existingUser->email],
                    ['token' => $token, 'created_at' => Carbon::now()]
                );

                $verificationUrl = url("/api/verify-email/{$token}");

                Mail::send('emails.verify-email', [
                    'url' => $verificationUrl,
                    'name' => $existingUser->name
                ], function ($message) use ($existingUser) {
                    $message->to($existingUser->email)
                        ->subject('Verify your email address');
                });

                return makeResponse(SUCCESS_CODE, 'Your previous unverified account was updated. Please check your email to verify.');
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $role->id,
                'role'     => $role->name,
            ]);
            $user->syncRoles($role->name);

            UserInformation::create([
                'user_id'               => $user->id,
                'phone_number'          => $request->phone_number,
                'time_frame_for_immigration' => $request->time_frame_for_immigration,
                'address'               => $request->address,
                'country_name'          => $country->name,
                'country_id'            => $country->id,
                'city'                  => $request->city,
                'state_name'            => $state->name,
                'state_id'              => $state->id,
                'zipcode'               => $request->zipcode,
                'register_news_letter'  => $request->register_news_letter,
                'subscribe_for_newsletter' => $request->subscribe_for_newsletter,
            //   /  'image'                 => $imageSavePath,
            ]);

            $token = Str::random(60);
            PasswordResetTokens::updateOrInsert(
                ['email' => $user->email],
                ['token' => $token, 'created_at' => Carbon::now()]
            );
            $verificationUrl = url("/api/verify-email/{$token}");

            Mail::send('emails.verify-email', [
                'url' => $verificationUrl,
                'name' => $user->name
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Verify your email address');
            });
            return makeResponse(SUCCESS_CODE, 'Registration successful. Please check your email to verify your account.');
        });

        return $response;
    } catch (\Exception $e) {
        return makeResponse(FAILURE_CODE, $e->getMessage());
    }
}


        public function verifyEmail($token)
        {

            try {
                $record = PasswordResetTokens::where('token', $token)->first();

                // if (!$record) {
                //     return redirect(env('FRONTEND_URL') . '/login?verified=0');
                // }

                if (!$record) {
                   // return makeResponse(ABORT_CODE, NOT_FOUND);
                    return redirect(FRONTENDURL  . '/signin');
                }

                $user = User::where('email', $record->email)->first();

                if (!$user) {
                    return makeResponse(ABORT_CODE, NOT_FOUND);
                }

                if (!$user->email_verified_at) {
                    $user->email_verified_at = Carbon::now();
                    $user->save();

                    PasswordResetTokens::where('email', $user->email)->delete();

                    return redirect(FRONTENDURL . '/signin');
                }else {
                    // Email already verified
                    return redirect(FRONTENDURL . '/signin');
                }
            } catch (\Exception $e) {
                return redirect(FRONTENDURL  . '/signin');
            }
        }


        public function updateProfile(Request $request)
        {
            $user = Auth::user();

            if (!$user) {
                return makeResponse(ABORT_CODE, UNAUTHORIZED);
            }

            $validator = Validator::make($request->all(), [
                'name'             => 'nullable|string|max:255',
                'phone_number'     => 'nullable|max:20',
                'about'            => 'required',
                'licensed_states'  => 'nullable|array|max:3',
                'licensed_states.*'=> 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return makeResponse(FAILURE_CODE, 'Validation failed', $validator->errors());
            }

            try {
                $user->name = $request->name;
                $user->about = $request->about;
                $user->save();

                $licensedStates = array_slice(array_filter($request->input('licensed_states', [])), 0, 3);

                $userInfo = $user->userInformation;

                if ($userInfo) {
                    $userInfo->phone_number    = $request->phone_number;
                    $userInfo->licensed_states = $licensedStates;
                    $userInfo->save();
                } else {
                    $user->userInformation()->create([
                        'phone_number'    => $request->phone_number,
                        'licensed_states' => $licensedStates,
                    ]);
                }

                return makeResponse(SUCCESS_CODE, 'Profile updated successfully', [
                    'user' => $user->fresh()->load('userInformation'),
                ]);

            } catch (\Exception $e) {
                return makeResponse(500, 'Failed to update profile', ['error' => $e->getMessage()]);
            }
        }
}

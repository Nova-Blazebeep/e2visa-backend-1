<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\PasswordResetTokens;
use App\Models\State;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\UserInformation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

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
        // Uniqueness is NOT enforced here: an existing-but-unverified account for
        // this email is a valid, expected case (handled below — the account is
        // updated and a fresh verification email is sent). Blocking it at the
        // validation layer would make that unverified-account recovery path
        // unreachable and leave the user stuck with no way to finish signing up.
        'email'    => ['required', 'email'],
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
        // The transaction only covers DB writes. Sending the verification email
        // is deliberately done AFTER it commits (see below) — a transient SMTP
        // hiccup must never roll back an otherwise-successful account creation.
        $outcome = DB::transaction(function () use ($request) {
            // Include soft-deleted rows: MySQL's unique index on `email` doesn't
            // know about deleted_at, so a soft-deleted account still physically
            // occupies the address and would otherwise block re-registration
            // with a raw duplicate-key error. A deleted account should never
            // hold an email hostage, so purge it here and proceed as a fresh signup.
            $existingUser = User::withTrashed()->where('email', $request->email)->first();
            if ($existingUser && $existingUser->trashed()) {
                UserInformation::where('user_id', $existingUser->id)->forceDelete();
                PasswordResetTokens::where('email', $existingUser->email)->delete();
                $existingUser->forceDelete();
                $existingUser = null;
            }

            $role = Role::findOrFail($request->role_id);
            $country = Country::findOrFail($request->country_id);
            $state = State::findOrFail($request->state_id);

            // $imagePath = null;
            // if ($request->hasFile('image')) {
            //     $imagePath = $request->file('image')->store('user_images', 'public');
            //     $imageSavePath = str_replace('public/', '', $imagePath);
            // }


            if ($existingUser && $existingUser->updated_at >= Carbon::now()->subMinutes(10)) {
                return ['response' => makeResponse(NOT_ACCEPTABLE, 'Please wait at least 10 minutes before reattempt.')];
            }

            if ($existingUser) {
                if ($existingUser->email_verified_at) {
                    return ['response' => makeResponse(NOT_ACCEPTABLE, 'Email already registered and verified. Please log in.')];
                }

                $existingUser->update([
                    'name'     => $request->name,
                    'password' => Hash::make($request->password),
                    'role_id'  => $role->id,
                    'role'     => $role->name,
                ]);
                $existingUser->syncRoles($role->name);
                $this->ensurePendingBadge($existingUser, $role->name);

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

                return [
                    'response' => makeResponse(SUCCESS_CODE, 'Your previous unverified account was updated. Please check your email to verify.'),
                    'mail' => ['email' => $existingUser->email, 'name' => $existingUser->name, 'url' => url("/api/verify-email/{$token}")],
                ];
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $role->id,
                'role'     => $role->name,
            ]);
            $user->syncRoles($role->name);
            $this->ensurePendingBadge($user, $role->name);

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

            return [
                'response' => makeResponse(SUCCESS_CODE, 'Registration successful. Please check your email to verify your account.'),
                'mail' => ['email' => $user->email, 'name' => $user->name, 'url' => url("/api/verify-email/{$token}")],
            ];
        });

        // Account is safely committed at this point. A failure here must not
        // undo it or block the signup — just log it; the user can always
        // retry via /api/resend-verification-email.
        if (!empty($outcome['mail'])) {
            try {
                Mail::send('emails.verify-email', [
                    'url' => $outcome['mail']['url'],
                    'name' => $outcome['mail']['name'],
                ], function ($message) use ($outcome) {
                    $message->to($outcome['mail']['email'])->subject('Verify your email address');
                });
            } catch (\Throwable $e) {
                \Log::error('Verification email failed to send during registration: ' . $e->getMessage());
            }
        }

        return $outcome['response'];
    } catch (QueryException $e) {
        // Two near-simultaneous submissions can both pass the uniqueness
        // check before either commits; the second insert then hits the
        // DB-level unique constraint. Surface a friendly message instead
        // of the raw SQL error.
        if ($e->getCode() === '23000') {
            return makeResponse(NOT_ACCEPTABLE, 'This email is already registered. Please log in or use a different email.');
        }
        return makeResponse(FAILURE_CODE, 'Registration failed. Please try again.');
    } catch (\Exception $e) {
        // Never leak internal exception details (server paths, mail-host
        // errors, etc.) to the client — log it server-side and return a
        // generic message instead.
        \Log::error('Registration failed: ' . $e->getMessage());
        return makeResponse(FAILURE_CODE, 'Registration failed. Please try again.');
    }
}

// Buyers, Admins, and Moderators never need a paid badge — everyone else
// (Seller, Attorney, Business Broker, and the rest of the professional
// roles) gets a pending UserBadge row at signup, activated later by an
// admin once Mike's merchant services confirms payment. firstOrCreate
// keeps this idempotent for the re-registration-of-an-unverified-account
// path, where a badge row may already exist from a prior attempt.
private function ensurePendingBadge(User $user, string $roleName): void
{
    if (in_array($roleName, ['Buyer', 'Admin', 'Moderator'], true)) {
        return;
    }
    UserBadge::firstOrCreate(['user_id' => $user->id], ['status' => 'pending']);
}

public function resendVerificationEmail(Request $request)
{
    $rules = ['email' => 'required|email'];
    ValidateApiRequest($rules, $request->all());

    try {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return makeResponse(NOT_ACCEPTABLE, 'No account found with this email.');
        }

        if ($user->email_verified_at) {
            return makeResponse(NOT_ACCEPTABLE, 'This email is already verified. Please log in.');
        }

        $existingToken = PasswordResetTokens::where('email', $user->email)->first();
        if ($existingToken && $existingToken->created_at >= Carbon::now()->subSeconds(60)) {
            return makeResponse(NOT_ACCEPTABLE, 'A verification email was just sent. Please wait a minute before requesting another.');
        }

        $token = Str::random(60);
        PasswordResetTokens::updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        $verificationUrl = url("/api/verify-email/{$token}");

        Mail::send('emails.verify-email', [
            'url' => $verificationUrl,
            'name' => $user->name,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify your email address');
        });

        return makeResponse(SUCCESS_CODE, 'Verification email sent. Please check your inbox.');
    } catch (\Exception $e) {
        return makeResponse(FAILURE_CODE, 'Failed to send verification email. Please try again.');
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


        // Lightweight "refresh my own data" endpoint — lets the frontend pick up
        // changes made elsewhere (e.g. an admin activating a badge in the portal)
        // without forcing the user to sign out and back in. Same enrichment as
        // LoginController::login() so the response can be merged straight into
        // the locally-stored user object.
        public function me(Request $request)
        {
            $user = Auth::user();

            if (!$user) {
                return makeResponse(ABORT_CODE, UNAUTHORIZED);
            }

            $userInfo = UserInformation::where('user_id', $user->id)->first();
            $user['phone'] = $userInfo->phone_number ?? 'NA';
            $user['badge_status'] = in_array($user->role, ['Buyer', 'Admin', 'Moderator'], true)
                ? null
                : ($user->userBadge->status ?? null);
            $user['role_badge_icon'] = $user->activeBadgeIcon();

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, [
                'user' => $user->load('userInformation'),
            ]);
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
                'about'            => 'required|max:150',
                'licensed_states'  => 'nullable|array',
                'licensed_states.*'=> 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return makeResponse(FAILURE_CODE, 'Validation failed', $validator->errors());
            }

            try {
                $user->name = $request->name;
                // Kept in sync for backward compatibility — the admin panel
                // and public professional profile read about from
                // user_information.about (updated below), not this column.
                $user->about = $request->about;
                $user->save();

                $licensedStates = array_values(array_unique(array_filter($request->input('licensed_states', []))));

                $userInfo = $user->userInformation;

                if ($userInfo) {
                    $userInfo->phone_number    = $request->phone_number;
                    $userInfo->about           = $request->about;
                    $userInfo->licensed_states = $licensedStates;
                    $userInfo->save();
                } else {
                    $user->userInformation()->create([
                        'phone_number'    => $request->phone_number,
                        'about'           => $request->about,
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

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // users table fields
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
            'role_id'     => 'required|string',
            // user_information table fields
            'phone_number'              => 'required|numeric',
            'time_frame_for_immigration' => 'nullable|string|max:255',
            'address'                   => 'nullable|string|max:255',
            'country_id'                   => 'required|string|max:100',
            'city'                      => 'nullable|string|max:100',
            'state_id'                     =>'required|string|max:100',
            'zipcode'                   => 'required|string|max:20',
            'subscribe_for_newsletter'     => 'required|boolean',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BadgeRequest extends FormRequest
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
        $badgeId = $this->route('id'); 

        return [
            'role_id' => 'required|exists:roles,id|unique:badges,role_id,' . $badgeId,
            'icon' => $badgeId ? 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'role_id.unique' => 'This role already has a badge. Please select a different role.',
            'role_id.required' => 'Role is required.',
            'role_id.exists' => 'Selected role does not exist.',
            'icon.required' => 'Icon image is required.',
            'icon.image' => 'Uploaded file must be an image.',
            'icon.mimes' => 'Allowed image formats: jpeg, png, jpg, gif.',
            'icon.max' => 'Maximum allowed image size is 2MB.',
        ];
    }
}

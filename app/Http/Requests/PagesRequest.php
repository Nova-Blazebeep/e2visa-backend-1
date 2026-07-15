<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagesRequest extends FormRequest
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
        $pageId = $this->route('page'); 

        return [
            'page_name'    => 'required|string|max:255|unique:pages,page_name,' . $pageId,
            'page_slug'    => 'required|string|max:255|unique:pages,page_slug,' . $pageId,
            'page_content' => 'required|string',
        ];
    }
}

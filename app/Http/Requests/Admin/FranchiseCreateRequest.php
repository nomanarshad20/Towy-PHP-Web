<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FranchiseCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'lat' => 'required',
            'lng' => 'required'

        ];
    }

    public function messages()
    {
        return [
            'lat.required' => 'Please Select Address Google Map Suggestion',
            'lng.required' => 'Please Select Address Google Map Suggestion'
        ];
    }
}

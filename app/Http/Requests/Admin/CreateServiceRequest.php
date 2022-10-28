<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceRequest extends FormRequest
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
            'initial_time_rate' => 'required',
            'initial_distance_rate' => 'required',
            'base_rate' => 'required',
            'service_time_rate' => 'required',
            'image' => 'mimes:jpeg,png,jpg'
        ];

    }
}

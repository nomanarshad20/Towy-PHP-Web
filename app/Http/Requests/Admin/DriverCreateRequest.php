<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DriverCreateRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',

//            'franchise_id' => 'required',
            'city' => 'required',

            'vehicle_name' => 'required_if:user_type,2',
            'model' => 'required_if:user_type,2',
            'model_year' => 'required_if:user_type,2',
            'registration_number' => 'required_if:user_type,2',

        ];
    }
}

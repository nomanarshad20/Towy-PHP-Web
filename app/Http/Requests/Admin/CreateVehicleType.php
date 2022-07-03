<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateVehicleType extends FormRequest
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
            'per_min_rate' => 'required',
            'per_km_rate' => 'required',
            'min_fare' => 'required',
            'tax_rate' => 'required',
            'waiting_price_per_min' => 'required',
            'initial_distance_rate' => 'required',
            'initial_time_rate' => 'required'
        ];
    }
}

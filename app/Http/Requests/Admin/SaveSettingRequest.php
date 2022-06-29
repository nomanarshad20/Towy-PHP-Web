<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveSettingRequest extends FormRequest
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
            'search_range' => 'required',
            'cancel_ride_time' => 'required',
            'driver_cancel_fine_amount' => 'required',
            'passenger_cancel_fine_amount' => 'required',
            'allowed_waiting_time' => 'required'
        ];
    }
}

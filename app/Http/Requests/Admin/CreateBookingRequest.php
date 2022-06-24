<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
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
            'passenger_id' => 'required',
//            'vehicle_type_id' => 'required',

            'pick_up_area' => 'required',
            'pick_up_lat' => 'required',
            'pick_up_lng' => 'required',

            'drop_off_area' => 'required',
            'drop_off_lat' => 'required',
            'drop_off_lng' => 'required',

            'booking_type' => 'required',

            'pick_up_date' => 'required_if:booking_type,book_later',
            'pick_up_time' => 'required_if:booking_type,book_later'


        ];
    }
}

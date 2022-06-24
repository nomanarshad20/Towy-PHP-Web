<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'pick_up_area' => 'required',
            'pick_up_latitude' => 'required',
            'pick_up_longitude' => 'required',
            'drop_off_area' => 'required',
            'drop_off_latitude' => 'required',
            'drop_off_longitude' => 'required',
            'payment_type' => 'required',
            'estimated_fare' => 'required',
            'total_distance' => 'required',
            'booking_type' => 'required',
            'pick_up_date' => 'required_if:booking_type,book_later',
            'pick_up_time' => 'required_if:booking_type,book_later',
//                'driver_status' => 'required',
//                'ride_status' => 'required',
//                'description' => 'required'
            'vehicle_type_id' => 'required',
            //            'vehicle_id'        => 'required',
            //            'booking_unique_id' => 'required'
            //            'total_distance'   => 'required',
            //            'actual_fare'        => 'required',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            makeResponse('error', $validator->errors()->first(), 422)
        );
    }
}

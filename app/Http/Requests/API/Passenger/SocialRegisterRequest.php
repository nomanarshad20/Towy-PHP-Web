<?php

namespace App\Http\Requests\API\Passenger;

use Illuminate\Foundation\Http\FormRequest;

class SocialRegisterRequest extends FormRequest
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
//            'first_name' => 'required',
//            'last_name' => 'required',
            'email' => 'required|email',
            'user_type' => 'required',
            'fcm_token' => 'required',
            'provider' => 'required',
            'social_uid' => 'required'
        ];
    }
}

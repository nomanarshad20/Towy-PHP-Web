<?php

namespace App\Http\Requests\API\Passenger;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
//            'email' => 'required|email|unique:users',
            'password' => 'required',
            'user_type' => 'required',
            'fcm_token' => 'required',
//            'mobile_no' => 'required|unique:users'
            'email' => 'required|email',
            'mobile_no' => 'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            makeResponse('error', $validator->errors()->first(),422)
        );
    }
}

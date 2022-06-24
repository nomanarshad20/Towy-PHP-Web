<?php

namespace App\Http\Requests\API\Driver;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DriverRegisterRequest extends FormRequest
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
            'mobile_no' => 'required|unique:users,mobile_no',
            'fcm_token' => 'required',
            'user_type' => 'required|in:2',
            'name' => 'required',
            'email' => 'required|email',
            'city' => 'required',
            'password' => 'required|min:8|confirmed'
        ];
    }

    public function messages()
    {
        return [
            'mobile_no.unique' => 'This mobile number is already registered. Please go to login screen'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            makeResponse('error', $validator->errors()->first(),422)
        );
    }
}

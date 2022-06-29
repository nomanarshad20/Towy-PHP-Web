<?php

namespace App\Http\Requests\API\Driver;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DriverLoginRequest extends FormRequest
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
            'mobile_no' => 'required|exists:users,mobile_no',
            'fcm_token' => 'required',
            'user_type' => 'required|in:2',
            'password' => 'min:8|required'
        ];
    }

    public function messages()
    {
        return [
            'mobile_no.exists' => "Account doesn't exist"
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            makeResponse('error', $validator->errors()->first(),422)
        );
    }
}

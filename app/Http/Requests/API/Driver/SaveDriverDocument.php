<?php

namespace App\Http\Requests\API\Driver;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SaveDriverDocument extends FormRequest
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
            'cnic_front_side' => 'mimes:jpg,jpeg,png',
            'cnic_back_side' => 'mimes:jpg,jpeg,png',
            'license_front_side' => 'mimes:jpg,jpeg,png',
            'license_back_side' => 'mimes:jpg,jpeg,png',
            'image' => 'mimes:jpg,jpeg,png',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            makeResponse('error', $validator->errors()->first(),422)
        );
    }
}

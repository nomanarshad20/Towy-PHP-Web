<?php

namespace App\Http\Requests\API\Driver;

use Illuminate\Foundation\Http\FormRequest;

class SaveSocialSecurityNumberRequest extends FormRequest
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
            'ssn' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'ssn.required' => 'Social Security Number is a Required Field'
        ];
    }
}

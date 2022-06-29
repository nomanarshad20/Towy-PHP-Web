<?php

namespace App\Http\Requests\API\Driver;

use Illuminate\Foundation\Http\FormRequest;

class DriverConnectionStatusRequest extends FormRequest
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
            'availability_status' => 'required|in:0,1'
        ];
    }
}

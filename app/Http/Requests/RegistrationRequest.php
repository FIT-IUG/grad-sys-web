<?php

namespace App\Http\Requests;

use App\Rules\FirebaseUniqueRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class RegistrationRequest extends FormRequest
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
            'name' => 'required:alpha',
            'email' => 'required|email',
            'mobile_number' => 'required|numeric|digits:10',
            'std' => ['required', 'numeric', 'digits:9', new FirebaseUniqueRule()],
            'department' => 'required',
            'password' => 'required|confirmed|digits_between:8,16',
        ];
    }
}

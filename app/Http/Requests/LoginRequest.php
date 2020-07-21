<?php

namespace App\Http\Requests;

use App\Rules\CheckEmailRule;
use App\Rules\CheckPasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
//        'regex:/(.*)@iugaza\.edu\.com$/i',
        return [
            'email' => ['required', new CheckEmailRule()],
            'password' => ['required', 'between:8,16', new CheckPasswordRule()]
        ];
    }
}

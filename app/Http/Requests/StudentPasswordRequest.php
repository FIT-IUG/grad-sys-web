<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentPasswordRequest extends FormRequest
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
//        'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
        return [
            'password' => ['required', 'confirmed','between:8,16','regex:/^.*(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/'],
            'password_confirmation' => ['required',],
        ];
    }
}

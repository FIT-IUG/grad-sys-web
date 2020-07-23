<?php

namespace App\Http\Requests;

use App\Rules\UniqueStudentDataRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterStudentRequest extends FormRequest
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
//        regex:/(.*)@iugaza\.edu\.ps/i
        return [
            'name' => ['required:alpha', new UniqueStudentDataRule()],
            'email' => ['required', 'email', new UniqueStudentDataRule()],
            'mobile_number' => ['required', 'numeric', 'digits:10', new UniqueStudentDataRule],
            'user_id' => ['required', 'numeric', 'digits:9', new UniqueStudentDataRule()],
            'department' => 'required',
        ];
    }
}

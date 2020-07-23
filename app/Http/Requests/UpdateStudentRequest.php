<?php

namespace App\Http\Requests;

use App\Rules\UpdateUserRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
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
//        regex:/(.*)@iugaza\.edu\.com/i
        return [
            'name' => ['required:alpha', new UpdateUserRule()],
            'email' => ['required', 'regex:/^[a-zA-Z0-9._-]+@(iugaza\.edu\.ps|gmail\.com)/', new UpdateUserRule()],
            'mobile_number' => ['required', 'numeric', 'digits:10', new UpdateUserRule()],
            'user_id' => ['required', 'numeric', 'digits:9', new UpdateUserRule()],
            'department' => 'required',
        ];
    }
}

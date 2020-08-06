<?php

namespace App\Http\Requests;

use App\Rules\CheckUserDepartmentAsRoleRule;
use App\Rules\CheckUserIDLengthRule;
use App\Rules\UniqueStudentDataRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => ['required:alpha', new UniqueStudentDataRule()],
            'email' => ['required', 'email', new UniqueStudentDataRule()],
            'mobile_number' => ['required', 'numeric', 'digits:10', new UniqueStudentDataRule],
            'user_id' => ['required', 'numeric', new UniqueStudentDataRule(), new CheckUserIDLengthRule()],
            'department' => ['required', new CheckUserDepartmentAsRoleRule()],
            'role' => 'required'
        ];
    }
}

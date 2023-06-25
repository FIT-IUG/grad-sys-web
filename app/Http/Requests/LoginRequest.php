<?php

namespace App\Http\Requests;

<<<<<<< HEAD
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest{
=======
use App\Rules\CheckEmailRule;
use App\Rules\CheckPasswordRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
>>>>>>> osama
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
<<<<<<< HEAD
            'email' => 'required|email',
            'password' => 'required|between:8,16'
=======
            'email' => ['required', 'email', new CheckEmailRule()],
            'password' => ['required', 'between:8,16', new CheckPasswordRule()]
>>>>>>> osama
        ];
    }
}

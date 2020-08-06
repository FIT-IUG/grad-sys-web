<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\ValidationData;

class CheckUserIDLengthRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $role = request()->get('role');
        if ($role == 'student')
            if (strlen($value) == 9)
                return true;
            else
                return false;


    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'رقم الطالب يجب ان يحتوي على 9 خانات.';
    }
}

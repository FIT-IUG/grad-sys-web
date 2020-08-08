<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckUserDepartmentAsRoleRule implements Rule
{
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
            if (strtoupper($value) == 'FIT')
                return false;
            else
                return true;
        else
            if (strtoupper($value) != 'FIT')
                return false;
            else
                return true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $role = request()->get('role');
        if ($role == 'student')
            return 'لا يمكن إختيار هذا التخصص للطالب.';
        else if ($role == 'teacher')
            return 'لا يمكن إختيار هذا التخصص للمدرس.';
        else
            return 'لا يمكن إختيار هذا التخصص للأدمن.';

    }
}

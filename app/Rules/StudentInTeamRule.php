<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Kreait\Firebase\Exception\ApiException;

class StudentInTeamRule implements Rule
{


    /**
     * Determine if the validation rule passes.
     *
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */

//    check if student is in team or not
    public function passes($attribute, $value)
    {
        $groups = firestoreCollection('groups')
            ->where($attribute, '=', $value)->documents()->rows();
        if ($groups != null) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'الطالب مسجل في مجموعة اخرى.';
    }
}

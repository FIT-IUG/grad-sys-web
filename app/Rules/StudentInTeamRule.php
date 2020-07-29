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
     * @throws ApiException
     */

//    check if student is in team or not
    public function passes($attribute, $value)
    {

        $groups = firebaseGetReference('groups')->getValue();
        foreach ($groups as $group)
            if ($value == $group['leaderStudentStd'])
                return false;
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'الطالب منضم لمجموعة أخرى.';
    }
}

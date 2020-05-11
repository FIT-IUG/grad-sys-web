<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Kreait\Firebase\Exception\ApiException;

class CheckEmailRule implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @throws ApiException
     */
    public function passes($attribute, $value)
    {
//        check if email is exist
//        $isEmpty = firestoreCollection('users')->where('email', '=', $value)->documents()->isEmpty();
        $users = firebaseGetReference('users')->getValue();
        foreach ($users as $user)
            if ($user['email'] == $value)
                return true;
        return false;
//            ->where('email', '=', $value)->documents()->isEmpty();
//        if ($isEmpty)
//            return false;
//        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'الايميل خاطئ.';
    }
}

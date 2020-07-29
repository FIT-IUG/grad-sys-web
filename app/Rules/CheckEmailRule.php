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
        $users = firebaseGetReference('users')->getValue();
        foreach ($users as $user)
            if (isset($user['email']) && $user['email'] == $value)
                return true;
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'البريد الإلكتروني خاطئ أو غير موجود.';
    }
}

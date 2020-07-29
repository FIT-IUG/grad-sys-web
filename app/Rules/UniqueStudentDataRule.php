<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Kreait\Firebase\Exception\ApiException;

class UniqueStudentDataRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     * @return RedirectResponse
     * @throws ApiException
     */
    public function passes($attribute, $value)
    {
//        if row has value there is a similar value.
        $users = firebaseGetReference('users')->getValue();
        foreach ($users as $user)
            if (isset($user[$attribute]) && $user[$attribute] == $value)
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
        return ':attribute مسجل مسبقًا.';
    }
}

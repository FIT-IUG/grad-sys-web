<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class UpdateUserRule implements Rule
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
     * @throws ApiException
     */
    public function passes($attribute, $value)
    {

        $users = firebaseGetReference('users')->getValue();
        Arr::forget($users, request()->segment(5));

        foreach ($users as $user) {
            if (isset($user[$attribute]) && $user[$attribute] == $value)
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
        return ':attribute مسجل مسبقًا.';
    }
}

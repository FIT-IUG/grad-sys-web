<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckEmailRule implements Rule
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
        $isEmpty = firestoreCollection('users')->where('email', '=', $value)->documents()->isEmpty();
        if ($isEmpty)
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
        return 'الايميل خاطئ.';
    }
}

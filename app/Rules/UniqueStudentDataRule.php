<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Kreait\Firebase\Exception\ApiException;

class UniqueStudentDataRule implements Rule
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
//        if row has value there is a similar value.
//        $users = firebaseCreateData()->getReference('users')->getValue();
        $row = app('firebase.firestore')->database()
            ->collection('students')->where($attribute, '=', $value)->documents()->rows();
        if ($row != null)
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
        return ':attribute مسجل مسبقا.';
    }
}

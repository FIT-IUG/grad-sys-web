<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;

class StudentsDifferentRule implements Rule
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
        $min_students = firebaseGetReference('settings/min_group_members')->getValue();
        $counter = 0;
        foreach ($value as $std)
            if ($std != null)
                $counter++;

        if ($counter >= $min_students)
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
        $min_students = firebaseGetReference('settings/min_group_members')->getValue();

        return 'الرجاء إضافة الحد الأدنى من عدد الأعضاء والذي هو: ' . $min_students .' .';
    }
}

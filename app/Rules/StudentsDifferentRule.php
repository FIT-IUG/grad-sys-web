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
        if (sizeof($value) != 0) {
            return true;
        }
        return false;
//        foreach ($value as $key => $std)
//            if ($std == null) {
//                return false;
//            }
//        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'الرجاء إضافة الحد الأدنى من عدد الأعضاء.';
    }
}

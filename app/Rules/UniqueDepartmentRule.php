<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class UniqueDepartmentRule implements Rule
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
        $departments = firebaseGetReference('departments')->getValue();
        if (request()->get('action') == 'update') {
            Arr::forget($departments, request()->get('department_key'));
        }
        foreach ($departments as $department)
            if (isset($department) && $department == $value)
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
        return 'التخصص هذا موجود مسبقا.';
    }
}

<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Kreait\Firebase\Exception\ApiException;

class MinimumGroupMembersRule implements Rule
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
        $min_group_members = firebaseGetReference('settings/min_group_members')->getValue();
        if (sizeof($value) >= $min_group_members)
            return true;
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     * @throws ApiException
     */
    public function message()
    {
        $min_group_members = firebaseGetReference('settings/min_group_members')->getValue();
        return 'الحد الأدنى لعدد أعضاء الفريق' . $min_group_members;
    }
}

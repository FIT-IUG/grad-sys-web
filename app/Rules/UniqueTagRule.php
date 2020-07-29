<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class UniqueTagRule implements Rule
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
        $tags = firebaseGetReference('tags')->getValue();
        if (request()->get('action') == 'store') {
            foreach ($tags as $tag)
                if (isset($tag) && $tag == $value)
                    return false;
            return true;
        } elseif (request()->get('action') == 'update') {
            Arr::forget($tags, request()->get('tag_key'));
            foreach ($tags as $tag)
                if (isset($tag) && $tag == $value)
                    return false;
            return true;
        } else {
            return false;
        }

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'شكل المشروع هذا موجود مسبقا.';
    }
}

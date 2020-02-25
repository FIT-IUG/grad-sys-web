<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseUniqueRule implements Rule
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
        $jsonLink = serviceaccount::fromJsonFile(app_path('Http\Controllers\Firebase\firebaseKey.json'));
        $firebase = (new factory)
            ->withserviceaccount($jsonLink)
            ->withdatabaseuri('https://fugg-system.firebaseio.com')
            ->createDatabase();
        $reference = $firebase->getReference('users');
        [$keys, $values] = Arr::divide($reference->getValue());
        foreach ($keys as $k) {
            if ($k == $value) {
                return false;
            }
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
        return trans('validation.unique');
    }
}

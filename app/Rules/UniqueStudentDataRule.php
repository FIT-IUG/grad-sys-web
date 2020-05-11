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
     */
    public function passes($attribute, $value)
    {
//        if row has value there is a similar value.
//        $row = firestoreCollection('users')->where('role', '=', 'student')
//            ->where($attribute, '=', $value)->documents()->rows();
        try {
            $users = firebaseGetReference('users')->getValue();
            foreach ($users as $user) {
                if ($user['role'] == 'student') {
                    if ($user[$attribute] == $value)
                        return false;
                }
            }
            return true;
        } catch (ApiException $e) {
        }catch (\ErrorException $exception){
            return redirect()->back()->with('error','حدثت مشكلة في التسجيل.');
        }

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

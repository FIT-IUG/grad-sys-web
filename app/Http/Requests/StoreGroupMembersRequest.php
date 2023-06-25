<?php

namespace App\Http\Requests;

use App\Rules\StudentInTeamRule;
use App\Rules\StudentsDifferentRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class StoreGroupMembersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'leaderStudentStd' => ['required', new StudentInTeamRule()],
            'membersStd' => ['required', new StudentsDifferentRule()],
            'department' => ['required'],
            'graduateInFirstSemester' => ['required'],
        ];

    }
}

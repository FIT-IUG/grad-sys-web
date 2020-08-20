<?php

namespace App\Exports;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsNotInGroupExport implements FromCollection
{
    /**
     * @return Collection
     */
    public function collection()
    {
        $students = getUserByRole('student');
        $studentsStdInGroup = getStudentsStdInGroups();
        $students_std = [];
        $index = 0;
        $go_students = [];

        foreach ($students as $student) {
            Arr::set($students_std, $index++, $student['user_id']);
        }
        $students_not_in_group_array = array_diff($students_std, $studentsStdInGroup);
        $index = 0;

        foreach ($students as $student) {
            foreach ($students_not_in_group_array as $id) {
                if ($student['user_id'] == $id) {
                    $data = Arr::except($student, ['role', 'email', 'remember_token']);
                    Arr::set($go_students, $index++, $data);
                    break;
                }
            }
        }
//        dd($go_students);
        return new Collection($go_students);
    }
}

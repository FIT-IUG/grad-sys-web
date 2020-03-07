<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StudentsImport implements ToCollection
{
    /**
     * @param Collection $collection
     * @return Collection
     */
    public function collection(Collection $collection)
    {
        return new Collection([
            'name' => $collection[0],
            'email' => $collection[1],
            'std' => $collection[2],
            'mobile_number' => $collection[3],
            'department' => $collection[4],
        ]);
    }
}

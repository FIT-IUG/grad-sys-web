<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class TeachersImport implements ToCollection
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
            'mobile_number' => $collection[2],
        ]);
    }
}

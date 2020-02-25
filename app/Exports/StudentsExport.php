<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsExport implements FromCollection
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return new Collection([
            [
                'name' => 'name',
                'email' => 'osama@osama',
                'phone_number' => '0599999999'
            ],
            [
                'name' => 'name',
                'email' => 'osama@osama',
                'phone_number' => '0599999999'
            ],
            [
                'name' => 'name',
                'email' => 'osama@osama',
                'phone_number' => '0599999999'
            ],
        ]);
    }
}

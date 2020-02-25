<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToArray
{
    /**
     * @param Collection $collection
     * @return Collection
     */
    public function collection(Collection $collection)
    {
        return new Collection([
            'name' => 'osama',
            'email' => 'osama@osama',
            'phone_number' => '0599999999'
        ]);
    }

    public function model(array $row)
    {

    }

    /**
     * @inheritDoc
     */
    public function array(array $array)
    {
        return new Collection([
            'name' => 'osama',
            'email' => 'osama@osama',
            'phone_number' => '0599999999'
        ]);
    }
}

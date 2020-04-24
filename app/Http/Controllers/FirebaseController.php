<?php

namespace App\Http\Controllers;

class FirebaseController extends Controller
{
    public function firebase()
    {
        firestoreCollection('settings')->document(0)->delete();
        firestoreCollection('settings')->document(0)->create([
            'number_of_students_in_team' => 4,
        ]);
        return 'Ok!';
    }
}

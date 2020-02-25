<?php

namespace App\Http\Controllers\Firebase\Auth;

use App\Http\Controllers\Firebase\MainController;
use App\Http\Requests\RegistrationRequest;


class RegistrationController extends MainController
{
    public function index()
    {
        return view('register');
    }

    public function create(RegistrationRequest $request)
    {
        $validated = $request->validated();
        $this->firebase->getReference('users')->push($validated);
        return redirect()->route('login');
    }

}

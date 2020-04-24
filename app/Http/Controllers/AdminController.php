<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function settings()
    {
        $notifications = [];
        $statistics = '';
        return view('admin.settings')->with(['notifications' => $notifications, 'statistics' => $statistics]);
    }
}

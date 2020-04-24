<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupervisorController extends Controller
{
    public function replayToBeSupervisorRequest()
    {
//        dd(\request()->get('from'));
        $reply = \request()->get('reply');
        $student_std = request()->get('from');
        $teacher = \request()->get('to');
        if ($reply == 'accept') {
            $notification = firestoreCollection('notifications');
            $id = $notification->where('from', '=', $student_std)
                ->where('to', '=', $teacher)->documents()->rows()[0]->id();
            $notification->document($id)->update([['path' => 'isAccept', 'value' => 1]]);
            return redirect()->back()->with('success', 'تم قبول الطلب بنجاح.');
        } elseif ($reply == 'reject') {
            $notification = firestoreCollection('notifications');
            $id = $notification->where('from', '=', $student_std)
                ->where('to', '=', $teacher)->documents()->rows()[0]->id();
            $notification->document($id)->update([['path' => 'isAccept', 'value' => 0]]);
            return redirect()->back()->with('success', 'تم رفض الطلب بنجاح.');
        }
        return redirect()->back()->with('error', 'حصلت مشكلة في الطلب.');
    }
}

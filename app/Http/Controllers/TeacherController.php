<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function replayToBeSupervisorRequest()
    {
        $reply = request()->get('reply');
        $student_std = request()->get('from');
        $teacher_id = request()->get('to');
        $notifications = firebaseGetReference('notifications')->getValue();
        foreach ($notifications as $key => $notification) {
            if ($notification['from'] == $student_std && $notification['to'] == $teacher_id) {
                if ($reply == 'accept') {
                    firebaseGetReference('notifications/' . $key)->update(['isAccept' => '1']);
                    return redirect()->back()->with('success', 'تم قبول الطلب بنجاح.');
                }
                firebaseGetReference('notifications/' . $key)->update(['isAccept' => '-1']);
                return redirect()->back()->with('success', 'تم رفض الطلب بنجاح.');
            }
        }
        return redirect()->back()->with('error', 'حصلت مشكلة في الطلب.');
    }
}

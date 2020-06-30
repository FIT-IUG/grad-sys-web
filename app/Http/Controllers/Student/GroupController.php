<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupMembersRequest;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class GroupController extends Controller
{

    public function store(StoreGroupMembersRequest $request)
    {

//        $group_data = Arr::except($request->validated(), 'membersStd');
        $leader_std = getUserId();

        try {
            firebaseGetReference('groups')->push([
                'leaderStudentStd' => $leader_std,
                'graduateInFirstSemester' => $request->get('graduateInFirstSemester'),
                'membersStd' => ''
            ]);
            $students = getUserByRole('student');
            $leader_name = '';
            foreach ($students as $student) {
                if ($student['user_id'] == $leader_std) {
                    $leader_name = $student['name'];
                    break;
                }
            }

            //notification for every member to join group by event
            $members_std = array_filter($request->validated()['membersStd']);

//            dd($members_std);
            foreach ($members_std as $member_std) {
                firebaseGetReference('notifications')->push([
                    'from' => $leader_std,
                    'from_name' => $leader_name,
                    'to' => $member_std,
                    'type' => 'join_group',
                    'message' => 'طلب منك الطالب ' . $leader_name . ' الانضمام الى فريق التخرج الخاص بيه.',
                    'status' => 0,
                ]);
            }

            return redirect()->back()->with('success', 'تم ارسال الطلبات لاعضاء المجموعة.');
        } catch (ApiException $e) {
        }
    }

    public function memberResponse()
    {
        $reply = request()->get('reply');
        $leader_id = request()->get('from');
        $member_std = request()->get('to');
        $index = 0;
//        dd($member_std);

        $notifications = firebaseGetReference('notifications')->getValue();


        if ($reply == 'accept') {
            $groups = firebaseGetReference('groups')->getValue();
            foreach ($groups as $key => $group) {
                if ($group['leaderStudentStd'] == $leader_id) {
                    $members_std = firebaseGetReference('groups/' . $key)->getValue()['membersStd'];
                    if ($members_std == null)
                        firebaseGetReference('groups/' . $key)->update(['membersStd' => [$member_std]]);
                    else {
                        foreach ($members_std as $std)
                            Arr::set($stds, $index++, $std);
                        firebaseGetReference('groups/' . $key)->update(['membersStd' => $stds]);
                        foreach ($notifications as $key => $notification) {
                            if ($notification['to'] == $member_std && $notification['from'] == $leader_id) {
                                firebaseGetReference('notifications/' . $key)->update(['status' => 1]);
                            }
                        }
                        firebaseGetReference('notifications')->push([
                            'from' => $member_std,
                            'from_name' => 'student name',
                            'to' => $leader_id,
                            'type' => 'accept_join_team',
                            'message' => 'وافق السيد بتنجانة على طلب الانضمام للفريق',
                            'status' => 0,
                        ]);
                    }
                }
            }
            return view('student.dashboard', [
                'notifications' => null, 'message' => 'انتظر قائد الفريق ليكمل الاعدادات'])->with('success', 'تم قبول طلب الانضمام بالفريق بنجاح.');
        } else {
            firebaseGetReference('notifications')->push([
                'from' => $member_std,
                'from_name' => 'student name',
                'to' => $leader_id,
                'type' => 'accept_join_team',
                'message' => 'رفض الطالب سيد بتنجانه الانضمام الى فريق التخرج.',
                'status' => -1,
            ]);
            return view('student.dashboard')->with('success', 'تم رفض طلب الانضمام بنجاح.');
        }
    }

    public function storeGroupSupervisor(StoreGroupRequest $request)
    {
        $leader_data = firebaseGetReference('users/' . session()->get('uid'))->getValue();
        $leader_id = $leader_data['user_id'];
        $leader_name = $leader_data['name'];
        $groups = firebaseGetReference('groups')->getValue();

        foreach ($groups as $key => $group) {
            if ($group['leaderStudentStd'] == $leader_id) {
                firebaseGetReference('groups/' . $key)->update($request->validated());
                break;
            }
        }
        firebaseGetReference('notifications')->push([
            'from' => $leader_id,
            'from_name' => $leader_name,
            'to' => $request->get('teacher'),
            'message' => 'طلب منك الطالب ' . $leader_name . 'أن تكون مشرف فريقه.',
            'project_initial_title' => $request->get('initial_title'),
            'type' => 'to_be_supervisor',
            'status' => '0',
        ]);
        return view('student.dashboard')->with('success', 'تم ارسال الطلب بنجاح');
    }

}

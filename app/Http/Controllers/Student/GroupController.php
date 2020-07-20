<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExtraGroupMembersRequest;
use App\Http\Requests\StoreGroupMembersRequest;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kreait\Firebase\Exception\ApiException;

class GroupController extends Controller
{

    public function store(StoreGroupMembersRequest $request)
    {

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

            foreach ($members_std as $member_std) {
                firebaseGetReference('notifications')->push([
                    'from' => $leader_std,
                    'from_name' => $leader_name,
                    'to' => $member_std,
                    'type' => 'join_group',
                    'message' => 'طلب منك الطالب ' . $leader_name . ' الانضمام الى فريق التخرج الخاص بيه.',
                    'status' => 'wait',
                ]);
            }

//            return redirect()->back()->with('success', 'تم ارسال الطلبات لاعضاء المجموعة.');
            return redirect()->route('student.index')->with('success', 'تم ارسال الطلبات لاعضاء المجموعة.');
        } catch (ApiException $e) {
        }
    }

    public function storeExtra(StoreExtraGroupMembersRequest $request)
    {

        $leader_std = getUserId();
        $new_student_std = $request->get('membersStd');

        try {
            $groups = firebaseGetReference('groups')->getValue();

            foreach ($groups as $key => $group) {
                if ($group['leaderStudentStd'] == $leader_std) {
                    $members_std = Arr::collapse([$group['membersStd'], $new_student_std]);
                    firebaseGetReference('groups/' . $key)->update(['membersStd' => $members_std]);
                    break;
                }
            }

            $leader_name = getLeaderName();

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
                    'status' => 'wait',
                ]);
            }

            return redirect()->route('student.index')->with('success', 'تم ارسال الطلبات لاعضاء المجموعة.');
        } catch (ApiException $e) {
        }
    }

    public function memberResponse(Request $request)
    {

        try {
            $notification_key = $request->get('notification_key');
            $reply = $request->get('reply');
            $leader_id = $request->get('from');
            $member_std = $request->get('to');
            $students = getUserByRole('student');
            $max_members = firebaseGetReference('settings/max_members_std')->getValue();
            if ($reply == 'accept') {

                $groups = firebaseGetReference('groups');
                foreach ($groups->getValue() as $group_key => $group) {
                    if ($group['leaderStudentStd'] == $leader_id) {
                        $hasMemberStd = array_search($member_std, $group['membersStd']);
                        if (count($group['membersStd']) != $max_members) {
                            if ($hasMemberStd != null) {
                                foreach ($students as $student_key => $student) {
                                    if ($student['user_id'] == $member_std) {
                                        $groups->getChild($group_key)->getChild('membersStd/' . $student_key)->set($member_std);
                                        firebaseGetReference('notifications/' . $notification_key)->update(['status' => 'accept']);
                                        firebaseGetReference('notifications')->push([
                                            'from' => $member_std,
                                            'from_name' => 'student name',
                                            'to' => $leader_id,
                                            'type' => 'accept_join_team',
                                            'message' => 'وافق ' . $student['name'] . ' على طلب الانضمام للفريق.',
                                            'status' => 'readOnce',
                                        ]);
                                    }
                                }
                            } else {
                                return redirect()->back()->with('error', 'أنت متواجد في فريق, لا يمكنك الدخول في فريق آخر.');
                            }
                        } else {
                            if (Str::substr(getUserId(), 0, 1) == 1) {

                                return redirect()->back()->with('error', 'وصل الفريق الذي تحاول التسجيل به إلى الحد الاقصى.');
                            } else {

                                return redirect()->back()->with('error', 'وصل الفريق الذي تحاولين التسجيل به إلى الحد الاقصى.');
                            }

                        }

                    }
                }

                return redirect()->route('student.index')->with('success', 'تم قبول الطلب بنجاح.');
            } else {
                $member_name = '';
                foreach ($students as $student)
                    if ($student['user_id'] == $member_std) {
                        $member_name = $student['name'];
                        break;
                    }

                firebaseGetReference('notifications/' . $notification_key)->update(['status' => 'reject']);
                firebaseGetReference('notifications')->push([
                    'from' => $member_std,
                    'from_name' => 'student name',
                    'to' => $leader_id,
                    'type' => 'reject_join_team',
                    'message' => 'رفض ' . $member_name . ' الانضمام الى فريق التخرج.',
                    'status' => 'readOnce',
                ]);
                return redirect()->route('student.index')->with('success', 'تم رفض طلب الانضمام بنجاح.');
            }
        } catch (ApiException $e) {
            return '';
        }
    }

    public function storeGroupSupervisor(StoreGroupRequest $request)
    {

        $leader_data = firebaseGetReference('users/' . session()->get('uid'))->getValue();
        $leader_id = $leader_data['user_id'];
        $leader_name = $leader_data['name'];
        $groups = firebaseGetReference('groups')->getValue();
        $project_data = Arr::except($request->validated(), 'teacher');
        $notifications_keys = $request->get('notification_key');

        if ($notifications_keys)
            foreach ($notifications_keys as $key) {
                firebaseGetReference('notifications/' . $key)->update(['status' => 'read']);
            }

        foreach ($groups as $key => $group) {
            if ($group['leaderStudentStd'] == $leader_id) {
                firebaseGetReference('groups/' . $key)->update($project_data);
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
            'status' => 'wait',
        ]);
        return redirect()->route('student.index')->with('success', 'تم ارسال الطلب بنجاح');
    }

}

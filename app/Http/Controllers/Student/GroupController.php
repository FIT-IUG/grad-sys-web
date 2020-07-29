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
    public function __construct()
    {
        $this->middleware('checkRole');
    }

    public function store(StoreGroupMembersRequest $request)
    {

        $leader_std = getUserId();

        try {
            firebaseGetReference('groups')->push([
                'leaderStudentStd' => $leader_std . '',
                'graduateInFirstSemester' => $request->get('graduateInFirstSemester'),
                'status' => 'wait_min_members',
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

            //notification for every member to join admin by event
            $members_std = array_filter($request->validated()['membersStd']);

            foreach ($members_std as $member_std) {
                firebaseGetReference('notifications')->push([
                    'from' => $leader_std,
                    'from_name' => $leader_name,
                    'to' => $member_std,
                    'type' => 'join_group',
                    'message' => 'لقد طلب منك الطالب ' . $leader_name . ' الانضمام إلى فريق التخرج الخاص به.',
                    'status' => 'wait',
                ]);
            }

//            return redirect()->back()->with('success', 'تم ارسال الطلبات لاعضاء المجموعة.');
            return redirect()->route('student.index')->with('success', 'تم إرسال طلبات الانضمام لأعضاء المجموعة.');
        } catch (ApiException $e) {
        }
    }

    public function storeExtra(StoreExtraGroupMembersRequest $request)
    {

        $leader_std = getUserId();
        $new_student_std = $request->get('membersStd');

        try {
            $groups = firebaseGetReference('groups')->getValue();

            if ($groups != null)
                foreach ($groups as $key => $group) {
                    if ($group['leaderStudentStd'] == $leader_std) {
                        $members_std = Arr::collapse([$group['membersStd'], $new_student_std]);
                        firebaseGetReference('groups/' . $key)->update(['membersStd' => $members_std]);
                        break;
                    }
                }

            $leader_name = getLeaderName();

            //notification for every member to join admin by event
            $members_std = array_filter($request->validated()['membersStd']);

            foreach ($members_std as $member_std) {
                firebaseGetReference('notifications')->push([
                    'from' => $leader_std,
                    'from_name' => $leader_name,
                    'to' => $member_std,
                    'type' => 'join_group',
                    'message' => 'لقد طلب منك الطالب ' . $leader_name . ' الانضمام إلى فريق التخرج الخاص به.',
                    'status' => 'wait',
                ]);
            }

            return redirect()->route('student.index')->with('success', 'تم إرسال طلبات الانضمام لأعضاء المجموعة.');
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
            $members_count = 0;

            $max_members = firebaseGetReference('settings/max_group_members')->getValue();
            if ($reply == 'accept') {

                $groups = firebaseGetReference('groups');
                if ($groups != null)
                    foreach ($groups->getValue() as $group_key => $group) {
                        if ($group['leaderStudentStd'] == $leader_id) {
                            if (is_array($group['membersStd'])) {
                                $hasMemberStd = array_search($member_std, $group['membersStd']);
                                $members_count = count($group['membersStd']);
                            } else
                                $hasMemberStd = array_search($member_std, [$group['membersStd']]);
                            if ($members_count < $max_members) {
                                if ($hasMemberStd == false) {
                                    foreach ($students as $student_key => $student) {
                                        if ($student['user_id'] == $member_std) {
                                            firebaseGetReference('androidStudentsStdInGroups')->push($member_std);
                                            firebaseGetReference('groups/' . $group_key)->update(['status' => 'choose_teacher']);
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
<<<<<<< HEAD
                                if (Str::substr(getUserId(), 0, 1) == 1) {

                                    return redirect()->back()->with('error', 'وصل الفريق الذي تحاول التسجيل به إلى الحد الاقصى.');
                                } else {

                                    return redirect()->back()->with('error', 'وصل الفريق الذي تحاولين التسجيل به إلى الحد الاقصى.');
                                }

=======
                                return redirect()->back()->with('error', 'أنت منضم لفريق، لا يمكنك الانضمام لفريق آخر.');
                            }
                        } else {
                            if (Str::substr(getUserId(), 0, 1) == 1) {

                                return redirect()->back()->with('error', 'وصل الفريق الذي تحاول الانضمام إليه إلى الحد الأقصى من عدد الأعضاء.');
                            } else {

                                return redirect()->back()->with('error', 'وصل الفريق الذي تحاولين الانضمام إليه إلى الحد الأقصى من عدد الأعضاء.');
>>>>>>> ee3a44873b75501166e5074f6a3a16f38bae8eef
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
                    'message' => 'رفض ' . $member_name . ' الانضمام الى فريق التخرّج.',
                    'status' => 'readOnce',
                ]);
                return redirect()->route('student.index')->with('success', 'تم رفض طلب الانضمام.');
            }
        } catch (ApiException $e) {
            return '';
        }
    }

    public function storeGroupSupervisor(StoreGroupRequest $request)
    {
        $request_tags = $request->get('tags');
        $tags = [];
        foreach ($request_tags as $tag)
            Arr::set($tags, Str::random(15), $tag);
        $project_data = [
            'tags' => $tags,
            'initialProjectTitle' => $request->get('initialProjectTitle'),
            'status' => 'wait_teacher'
        ];
        $leader_data = firebaseGetReference('users/' . session()->get('uid'))->getValue();
        $leader_id = $leader_data['user_id'];
        $leader_name = $leader_data['name'];
        $groups = firebaseGetReference('groups')->getValue();
//        $project_data = Arr::except($project_title, 'teacher');
        $notifications_keys = $request->get('notification_key');
//        Arr::set($project_data, 'status', 'wait_teacher');
        if ($notifications_keys)
            foreach ($notifications_keys as $key) {
                firebaseGetReference('notifications/' . $key)->update(['status' => 'read']);
            }
        if ($groups != null)
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
            'message' => 'لقد طلب منك الطالب ' . $leader_name . 'أن تكون مشرفَ مشروع التخرّج لفريقه.',
            'project_initial_title' => $request->get('initial_title'),
            'type' => 'to_be_supervisor',
            'status' => 'wait',
        ]);
        return redirect()->route('student.index')->with('success', 'تم إرسال الطلب بنجاح');
    }

}

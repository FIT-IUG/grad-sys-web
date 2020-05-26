<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupMembersRequest;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class StudentController extends Controller
{
    public function index()
    {
        $departments = ['تطوير البرمجيات', 'علم الحاسوب', 'نظم المعلومات', 'مالتيميديا', 'موبايل', 'تكنولوجيا المعلومات'];//'','','','',''
        $teachers = ['المدرس 1', 'المدرس 2', 'المدرس 3'];
//        dd('this is index student controller');
        $students = firestoreCollection('students')->select('std');
//        dd($students);
        return view('group.index', ['departments' => $departments, 'teachers' => $teachers, 'students' => $students])//            ->with('departments', $departments)->with('teachers', $teachers);
            ;
    }

    public function create()
    {
        return view('group.create');
    }

    public function storeGroupMembers(StoreGroupMembersRequest $request)
    {
        try {
            firebaseGetReference('groups')->push($request->validated());
            $students = getUserByRole('student');
            $leader_std = $request->get('leaderStudentStd');
            $leader_name = '';
            foreach ($students as $student) {
                if ($student['user_id'] == $leader_std) {
                    $leader_name = $student['name'];
                    break;
                }
            }
            //notification for every member to join group by event
            $members_std = $request->validated()['membersStd'];

            foreach ($members_std as $member_std) {
                firebaseGetReference('notifications')->push([
                    'from' => $request->get('leaderStudentStd'),
                    'to' => $member_std,
                    'type' => 'join_team',
                    'message' => 'طلب منك الطالب ' . $leader_name . ' الانضمام الى فريق التخرج الخاص بيه.',
                    'isAccept' => 0,
                ]);
            }

            return redirect()->back()->with('success', 'تم ارسال الطلبات لاعضاء المجموعة.');
        } catch (ApiException $e) {
        }
    }

    public function acceptTeamJoinRequest()
    {
        $hh = firestoreCollection('notifications')
            ->where('from', '=', request()->get('from'))
            ->where('to', '=', request()->get('to'));
        $id = $hh->documents()->rows()[0]->id();
        firestoreCollection('notifications')->document($id)->update([['path' => 'isAccept', 'value' => 1]]);
        return redirect()->back()->with('success', 'تم الموافقة على طلب الإنضمام بنجاح.');
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
            'isAccept' => '0',
        ]);
        return redirect()->back()->with('success', 'تم ارسال الطلب بنجاح');
    }

}

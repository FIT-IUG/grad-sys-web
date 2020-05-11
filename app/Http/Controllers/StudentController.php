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
            firestoreCollection('groups')->newDocument()->create($request->validated());
            //notification for every member to join group by event
            $members_std = $request->validated()['membersStd'];
            foreach ($members_std as $member_std) {
                firestoreCollection('notifications')->newDocument()->create([
                    'from' => $request->get('leaderStudentStd'),
                    'to' => $member_std,
                    'type' => 'join_team',
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
        $leader_id = getStudentStd();
        $group_data = firestoreCollection('groups')->where('leaderStudentStd', '=', $leader_id);
        $data = Arr::collapse([$group_data->documents()->rows()[0]->data(), $request->validated()]);
        $id = $group_data->documents()->rows()[0]->id();
        firestoreCollection('groups')->document($id)->set($data);
        firestoreCollection('notifications')->newDocument()->create([
            'from' => $leader_id,
            'to' => $request->get('teacher'),
            'isAccept' => null,
            'type' => 'to_be_supervisor'
        ]);
        return redirect()->back()->with('success', 'تم ارسال الطلب بنجاح');
    }

}

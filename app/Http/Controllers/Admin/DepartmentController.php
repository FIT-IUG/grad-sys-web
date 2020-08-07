<?php

namespace App\Http\Controllers\Admin;

use App\Events\UpdateDepartmentEvent;
use App\Http\Controllers\MainController;
use App\Http\Requests\DepartmentRequest;
use Kreait\Firebase\Exception\ApiException;

class DepartmentController extends MainController
{
    public function update(DepartmentRequest $department)
    {
        $new_department_name = $department->get('department');
        $old_department = $department->get('old_department_name');
        try {
            if ($department->get('action') == 'store') {
                firebaseGetReference('departments')->push($new_department_name);
                return redirect()->route('admin.settings')
                    ->with('success', 'تم إضافة التخصص: ' . $new_department_name . ' بنجاح.');
            } elseif ($department->get('action') == 'update') {
                firebaseGetReference('departments')->update([$department->get('department_key') => $new_department_name]);
                $users = firebaseGetReference('users')->getValue();
                foreach ($users as $user_key => $user) {
                    if (isset($user['department']) && $user['department'] != null) {
                        if ($user['department'] == $old_department) {
                            firebaseGetReference('users/' . $user_key)->update(['department' => $new_department_name]);
                        }
                    }
                }
//                event(new UpdateDepartmentEvent($new_department_name, $old_department));

                return redirect()->route('admin.settings')
                    ->with('success', 'تم تحديث التخصص: ' . $new_department_name . ' بنجاح.');
            } else {
                return redirect()->route('admin.index')->with('error', 'لا يمكنك ذلك.');
            }
        } catch (ApiException $e) {
        }
    }

    public
    function destroy($department_key)
    {
        try {
            $department = firebaseGetReference('departments/' . $department_key)->getValue();
            firebaseGetReference('departments/' . $department_key)->remove();
            return redirect()->route('admin.settings')->with('success', 'تم حذف شكل المشروع ' . $department);
        } catch (ApiException $e) {
        }

    }
}

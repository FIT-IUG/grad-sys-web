<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\MainController;
use App\Http\Requests\DepartmentRequest;
use Illuminate\Support\Arr;
use Kreait\Firebase\Exception\ApiException;

class DepartmentController extends MainController
{
    public function update(DepartmentRequest $department)
    {
        $department_name = $department->get('department');
        $old_department = $department->get('old_department_name');
        try {
            if ($department->get('action') == 'store') {
                firebaseGetReference('departments')->push($department_name);
                return redirect()->route('admin.settings')
                    ->with('success', 'تم إضافة التخصص: ' . $department_name . ' بنجاح.');
            } elseif ($department->get('action') == 'update') {
                firebaseGetReference('departments')->update([$department->get('department_key') => $department_name]);
                $groups = firebaseGetReference('groups')->getValue();
                foreach ($groups as $group_key => $group) {
                    if (isset($group['departments']) && $group['departments'] != null) {
                        foreach ($group['departments'] as $key => $department) {
                            if ($department == $old_department) {
                                Arr::set($go_departments, $key, $department_name);
                                Arr::forget($group['departments'], $key);
                                $go_departments = Arr::collapse([$go_departments, $group['departments']]);
                                firebaseGetReference('groups/' . $group_key . '/departments')->update($go_departments);
                                firebaseGetReference('groups')
                                    ->getChild($group_key)
                                    ->getChild('departments')
                                    ->update([$key => $department_name]);
                            }
                        }
                    }
                }
                return redirect()->route('admin.settings')
                    ->with('success', 'تم تحديث التخصص: ' . $department_name . ' بنجاح.');
            } else {
                return redirect()->route('admin.index')->with('error', 'لا يمكنك ذلك.');
            }
        } catch (ApiException $e) {
        }
    }

    public function destroy($department_key)
    {
        try {
            $department = firebaseGetReference('departments/' . $department_key)->getValue();
            firebaseGetReference('departments/' . $department_key)->remove();
            return redirect()->route('admin.settings')->with('success', 'تم حذف شكل المشروع ' . $department);
        } catch (ApiException $e) {
        }

    }
}

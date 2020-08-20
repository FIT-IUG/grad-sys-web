@extends('layout')
@section('title', 'بيانات المجموعة')
@section('content')
    <div class="card card-primary card-tabs">
        <div class="card-header">
            <h3 class="card-title">
                بيانات المجموعات الخاصة بالمشرف {{isset($teacher_data['name']) ? $teacher_data['name'] : '-'}}
            </h3>
        </div>
        <div class="card-body">
            <div class="tab-content" id="custom-content-below-tabContent">
                <table class="table">
                    <thead>
                    <tr>
                        <th>الرقم الجامعي</th>
                        <th>اسم الطالب</th>
                        <th>رقم الموبايل</th>
                        <th>القسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الإعدادات</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{isset($group_leader_data['user_id']) ? $group_leader_data['user_id'] : 'لا يوجد'}}</td>
                        <td>{{isset($group_leader_data['name']) ? $group_leader_data['name'] : 'لا يوجد'}}
                            <span class="fa fa-star text-warning"></span></td>
                        <td>{{isset($group_leader_data['mobile_number']) ? $group_leader_data['mobile_number'] : 'لا يوجد'}}</td>
                        <td>{{isset($group_leader_data['department']) ? $group_leader_data['department'] : 'لا يوجد'}}</td>
                        <td>{{isset($group_leader_data['email']) ? $group_leader_data['email'] : 'لا يوجد'}}</td>

                    </tr>
                    @foreach($group_members_data as $key => $member)
                        <tr>
                            <td>{{isset($member['user_id']) ? $member['user_id'] : 'لا يوجد'}}</td>
                            <td>{{isset($member['name']) ? $member['name'] : 'لا يوجد'}}</td>
                            <td>{{isset($member['mobile_number']) ? $member['mobile_number'] : 'لا يوجد'}}</td>
                            <td>{{isset($member['department']) ? $member['department'] : 'لا يوجد'}}</td>
                            <td>{{isset($member['email']) ? $member['email'] : 'لا يوجد'}}</td>
                            <td>
                                <a class="btn btn-danger btn-sm delete-confirm"
                                   href="{{route('admin.group.destroy.member',['group_key'=>$group_key, 'member_key'=>$key])}}">
                                    <i class="fa fa-trash">
                                    </i>
                                    حذف
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-6">
                        <p class="lead">بيانات المشرف</p>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th>الرقم الجامعي:</th>
                                    <td>{{isset($teacher_data['user_id']) ? $teacher_data['user_id'] : 'لا يوجد'}}</td>
                                </tr>
                                <tr>
                                    <th>الاسم:</th>
                                    <td>{{isset($teacher_data['name']) ? $teacher_data['name'] : 'لا يوجد'}}</td>
                                </tr>
                                <tr>
                                    <th>رقم الهاتف:</th>
                                    <td>{{isset($teacher_data['mobile_number']) ? $teacher_data['mobile_number'] : 'لا يوجد'}}</td>
                                </tr>
{{--                                <tr>--}}
{{--                                    <th>القسم:</th>--}}
{{--                                    <td>{{isset($teacher_data['department']) ? $teacher_data['department'] : 'لا يوجد'}}</td>--}}
{{--                                </tr>--}}
                                <tr>
                                    <th>البريد الإلكتروني:</th>
                                    <td>{{isset($teacher_data['email']) ? $teacher_data['email'] : '-'}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-6">
                        <p class="lead">بيانات المشروع</p>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th>العنوان المبدئي:</th>
                                    <td>{{isset($project_data['initialProjectTitle']) ? $project_data['initialProjectTitle'] : 'لا يوجد'}}</td>
                                </tr>
                                <tr>
                                    <th>هل المجموعة خريجة فصل أول؟</th>
                                    <td>{{(isset($project_data['graduateInFirstSemester']) ? $project_data['graduateInFirstSemester'] : '-') == 0 ? 'لا' : 'نعم'}}</td>
                                </tr>
                                <tr>
                                    <th>شكل المشروع:</th>
                                    <td>@if(isset($project_data['tags']) && $project_data['tags'] != null)
                                            @foreach($project_data['tags'] as $tag)
                                                @if($loop->last) {{$tag}}.
                                                @else {{$tag}},
                                                @endif
                                            @endforeach
                                        @else
                                            لا يوجد
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">إضافة أعضاء إلى الفريق</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.group.update',['group_key'=>$group_key])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>الرقم الجامعي للطالب </label>
                                <select class="students-std form-control select2 select2-hidden-accessible"
                                        name="student_id"
                                        style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true">
                                    <option value=""></option>
                                    @foreach($students as $key => $student)
                                        <option value="{{$key}}" id="option">
                                            {{$student}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">إضافة</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">تغيير مشرف المجموعة</h3>
                </div>
                <div class="card-body">
                    @error('membersStd')
                    <div class="alert alert-danger">
                        {{$message}}
                    </div>
                    @enderror
                    <form action="{{route('admin.group.update.teacher',['group_key'=>$group_key])}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>مشرف المجموعة</label>
                            <select class="form-control select2 select2-hidden-accessible" style="width: 100%;"
                                    tabindex="-1" aria-hidden="true" dir="rtl" name="teacher_id">
                                <option value=""></option>
                                @if(isset($teachers) && $teachers != null)
                                    @foreach($teachers as $teacher)
                                        <option value="{{$teacher['user_id']}}">
                                            {{$teacher['name']}}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>لا يوجد مشرفين متاحين</option>
                                @endif
                            </select>
                            @error('teacher_id')
                            <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">تغيير</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">تغيير قائد الفريق</h3>
                </div>
                <div class="card-body">
                    <form
                        action="{{route('admin.group.change.leader',['group_key'=>$group_key,'old_leader_key'=>$group_leader_data['key']])}}"
                        method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>القائد الجديد </label>
                                <select class="students-std form-control select2 select2-hidden-accessible"
                                        name="student_id"
                                        style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true">
                                    <option value=""></option>
                                    @foreach($group_members_data as $key => $member)
                                        <option value="{{$key}}"
                                                id="option">
                                            {{isset($member['name']) ? $member['name'] : 'لا يوجد'}}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">تغيير</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">تعديل بيانات المشروع</h3>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.group.update.project',['group_key'=>$group_key])}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="">تعديل عنوان المشروع</label>
                                <input type="text"
                                       class="form-control @error('projectTitle') is-invalid @enderror"
                                       name="projectTitle" style="border-color: #aaa;padding: 8px;"
                                       value="{{isset($project_data['initialProjectTitle']) ? $project_data['initialProjectTitle'] : 'لا يوجد'}}">
                                @error('projectTitle')
                                <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label>تغيير نوع المشروع</label>
                                <select class="form-control select2 select2-hidden-accessible" multiple=""
                                        name="tags[]"
                                        style="width: 100%;text-align: right;direction: rtl" tabindex="-1"
                                        aria-hidden="true">
                                    <option></option>
                                    @if(isset($tags) && $tags != null)
                                        @foreach($tags as $tag)
                                            <option value="{{$tag}}"
                                                    @if(isset($project_data['tags']) && $project_data['tags'] != null)
                                                    @foreach($project_data['tags'] as $selected_tag)
                                                    @if($tag == $selected_tag) selected @endif
                                                @endforeach
                                                @endif
                                            >{{$tag}}</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>لا يوجد خيارات متاحة</option>
                                    @endif
                                </select>
                                @error('tags')
                                <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">تعديل</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('.delete-confirm').on('click', function (event) {
            event.preventDefault();
            const url = $(this).attr('href');
            swal({
                title: 'هل تريد حذف العضو حقًا؟',
                text: 'هذا سيقوم بحذف العضو نهائيًا!',
                icon: 'warning',
                buttons: ["إلغاء", "نعم"],
            }).then(function (value) {
                if (value) {
                    window.location.href = url;
                }
            });
        });
    </script>
@endpush

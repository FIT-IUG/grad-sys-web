@extends('layout')
@section('title','الصفحة الطالب الرئيسية')
@section('content')

    <div class="row">
        <h1>{{isset($message) ? $message : ''}}</h1>
    </div>
    @if(isset($notifications) and $notifications != null && $notifications[array_key_first($notifications)]['status'] != 'readOnce')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">الاشعارات</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>الرسالة</th>
                                <th>الرد</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notifications as $key => $notification)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{isset($notification['message']) ? $notification['message'] : '-'}}</td>
                                    <td>
                                        <form
                                            action="{{route('student.admin.response',['from'=>$notification['from'],'to'=>$notification['to']])}}"
                                            method="post">
                                            @csrf
                                            <input type="text" value="{{$key}}" name="notification_key" hidden>
                                            <button class="btn btn-success" name="reply" value="accept">قبول</button>
                                            <button class="btn btn-danger" name="reply" value="reject">رفض</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(isset($group_members_data))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">بيانات أعضاء الفريق</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>الرقم الجامعي</th>
                                <th>رقم الجوال</th>
                                <th>الايميل</th>
                                <th>التخصص</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($group_members_data as $member)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{isset($member['name']) ? $member['name'] : 'لا يوجد' }}</td>
                                    <td>{{isset($member['user_id']) ? $member['user_id'] : 'لا يوجد' }}</td>
                                    <td>{{isset($member['mobile_number']) ? $member['mobile_number'] : 'لا يوجد' }}</td>
                                    <td>{{isset($member['email']) ? $member['email'] : 'لا يوجد' }}</td>
                                    <td>{{isset($member['department']) ? $member['department'] : 'لا يوجد' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        @if(isset($teacher_data))
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">بيانات المشرف</h3>
                    </div>
                    <div class="card-body box-profile">
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item" style="border-top: none">
                                <b>اسم المشرف</b> <a
                                    class="float-left">{{isset($teacher_data['name']) ? $teacher_data['name'] : 'لا يوجد'}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>رقم الهاتف</b> <a
                                    class="float-left">{{isset($teacher_data['mobile_number']) ? $teacher_data['mobile_number'] : 'لا يوجد'}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>الايميل</b> <a
                                    class="float-left">{{isset($teacher_data['email']) ? $teacher_data['email'] : 'لا يوجد'}}</a>
                            </li>
                        </ul>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        @endif
        @if(isset($project_data))
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">بيانات المشروع</h3>
                    </div>
                    <div class="card-body box-profile">
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item" style="border-top: none">
                                <b>عنوان المشروع</b> <a
                                    class="float-left">{{isset($project_data['initialProjectTitle']) ? $project_data['initialProjectTitle'] : 'لا يوجد'}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>ستتخرجون في الفصل الاول</b> <a class="float-left">{{isset($project_data['graduateInFirstSemester']) ?
                                                        ($project_data['graduateInFirstSemester'] == 0 ? 'لا': 'نعم') : 'لا يوجد'}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>نوع المشروع</b> <a class="float-left">
                                    @if(isset($project_data['tags']))
                                        @foreach($project_data['tags'] as $tag)
                                            @if($loop->last)
                                                {{$tag . '.'}}
                                            @else
                                                {{$tag.', '}}
                                            @endif
                                        @endforeach
                                    @else
                                        لا يوجد
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if(isset($group_students_complete) && $group_students_complete != 0)
        <div class="row">
            <div class="col-md-12">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">إضافة باقي أعضاء الفريق</h3>
                    </div>
                    <div class="card-body">
                        @error('membersStd')
                        <div class="alert alert-danger">
                            {{$message}}
                        </div>
                        @enderror
                        <form action="{{route('student.admin.storeExtra')}}" method="POST">
                            @csrf
                            <spam style="display: none">{{$next_student = $group_students_complete}}</spam>
                            <div class="row">
                                @for($i = 0; $i < $group_students_complete; $i++)
                                    <div class="form-group col-md-6">
                                        <label for="exampleInputEmail1">الرقم الجامعي للعضو {{$next_student++}} </label>
                                        <select class="students-std form-control select2 select2-hidden-accessible"
                                                name="membersStd[]"
                                                style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true">
                                            <option value=""></option>
                                            @foreach($students as $student)
                                                <option @if(old('membersStd.'.$i) != null)
                                                        @if(old('membersStd.'.$i) == $student) selected
                                                        value="{{$student}}"
                                                        id="option"@endif
                                                    @endif>
                                                    {{$student}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endfor
                            </div>
                            <button type="submit" class="btn btn-primary">تسجيل</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

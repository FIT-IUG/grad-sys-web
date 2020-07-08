@extends('layout')
@section('title','الصفحة الطالب الرئيسية')
@section('content')

    <div class="row">
        <h1>{{isset($message) ? $message : ''}}</h1>
    </div>
    @if(isset($notifications) and $notifications != null)
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
                                            action="{{route('student.group.response',['from'=>$notification['from'],'to'=>$notification['to']])}}"
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

    @if(isset($group_data))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">بيانات المجموعة</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>رقم الجوال</th>
                                <th>الايميل</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($group_data as $member)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{isset($member['name']) ? $member['name'] : '-' }}</td>
                                    <td>{{isset($member['mobile_number']) ? $member['mobile_number'] : '-' }}</td>
                                    <td>{{isset($member['email']) ? $member['email'] : '-' }}</td>
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
                                    class="float-left">{{isset($teacher_data['name']) ? $teacher_data['name'] : '-'}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>رقم الهاتف</b> <a
                                    class="float-left">{{isset($teacher_data['mobile_number']) ? $teacher_data['mobile_number'] : '-'}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>الايميل</b> <a
                                    class="float-left">{{isset($teacher_data['email']) ? $teacher_data['email'] : '-'}}</a>
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
                                    class="float-left">{{isset($project_data['initialProjectTitle']) ? $project_data['initialProjectTitle'] : '-'}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>ستتخرجون في الفصل الاول</b> <a class="float-left">{{isset($project_data['graduateInFirstSemester']) ?
                                                        ($project_data['graduateInFirstSemester'] == 0 ? 'لا': 'نعم') : '-'}}</a>
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
        <div class="col-md-6">
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
                    <form action="{{route('student.group.storeExtra')}}" method="POST">
                        @csrf
                        @for($i = 0; $i < $group_students_complete; $i++)
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="exampleInputEmail1">الرقم الجامعي للعضو {{$i+2 }}</label>
                                    <select class="students-std form-control select2 select2-hidden-accessible"
                                            name="membersStd[]"
                                            style="width: 100%;text-align: right" tabindex="-1" aria-hidden="true">
                                        <option value=""></option>
                                        @foreach($students as $student)
                                            <option @if(old('membersStd')[$i] != null)
                                                    @if(old('membersStd')[$i] == $student) selected value="{{$student}}"
                                                    id="option"@endif
                                                @endif>
                                                {{$student}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endfor
                        <button type="submit" class="btn btn-primary">تسجيل</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

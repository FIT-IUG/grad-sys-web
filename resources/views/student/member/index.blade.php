@extends('layout')
@section('title','صفحة عضو الفريق الرئيسية')
@section('content')

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
                                <th>الاسم</th>
                                <th>الرقم الجامعي</th>
                                <th>رقم الموبايل</th>
                                <th>البريد الإلكتروني</th>
                                <th>التخصص</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>{{isset($group_leader_data['name']) ? $group_leader_data['name'] : 'لا يوجد' }}</td>
                                <td>{{isset($group_leader_data['user_id']) ? $group_leader_data['user_id'] : 'لا يوجد' }}</td>
                                <td>{{isset($group_leader_data['mobile_number']) ? $group_leader_data['mobile_number'] : 'لا يوجد' }}</td>
                                <td>{{isset($group_leader_data['email']) ? $group_leader_data['email'] : 'لا يوجد' }}</td>
                                <td>{{isset($group_leader_data['department']) ? $group_leader_data['department'] : 'لا يوجد' }}</td>
                                <td><span class="fa fa-star text-warning"></span></td>
                            </tr>
                            @foreach($group_members_data as $member)
                                <tr>
                                    <td>{{isset($member['name']) ? $member['name'] : 'لا يوجد' }}</td>
                                    <td>{{isset($member['user_id']) ? $member['user_id'] : 'لا يوجد' }}</td>
                                    <td>{{isset($member['mobile_number']) ? $member['mobile_number'] : 'لا يوجد' }}</td>
                                    <td>{{isset($member['email']) ? $member['email'] : 'لا يوجد' }}</td>
                                    <td>{{isset($member['department']) ? $member['department'] : 'لا يوجد' }}</td>
                                    <td></td>
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
                                <b>البريد الإلكتروني</b> <a
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
                                <b>هل ستتخرجون في الفصل الاول؟</b> <a class="float-left">{{isset($project_data['graduateInFirstSemester']) ?
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

@endsection

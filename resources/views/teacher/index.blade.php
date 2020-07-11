@extends('layout')
@section('title','الصفحة المدرس الرئيسية')
@section('content')
    @if($notifications != null)
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
                                <th>العنوان المبدئي للمشروع</th>
                                <th>الرد</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notifications as $key => $notification)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{isset($notification['message']) ? $notification['message'] : '-'}}</td>
                                    <td>{{isset($notification['initialProjectTitle']) ? $notification['initialProjectTitle'] : '-'}}</td>
                                    <td>
                                        <form
                                            action="{{route('group.teacher.replyRequest',['from'=>$notification['from'],'to'=>$notification['to']])}}"
                                            method="post">
                                            @csrf
                                            <input type="text" hidden value="{{$key}}" name="notification_key">
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
    @if($groups_data != null)
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    بيانات المجموعات الخاصة بك
                </h3>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                    @for($i = 1; $i <= sizeof($groups_data) ;$i++)
                        <li class="nav-item">
                            <a class="nav-link @if($i == 1) active @endif" id="custom-content-below-home-tab"
                               data-toggle="pill"
                               href="#group{{$i}}" role="tab" aria-controls="custom-content-below-home"
                               aria-selected="false">المجموعة {{ $i }}</a>
                        </li>
                    @endfor
                </ul>
                <div class="tab-content" id="custom-content-below-tabContent">
                    @foreach($groups_data as $key => $group)
                        <div class="tab-pane fade @if($loop->first) active show @endif" id="group{{$key+1}}"
                             role="tabpanel"
                             aria-labelledby="custom-content-below-home-tab">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>الرقم الجامعي</th>
                                    <th>اسم الطالب</th>
                                    <th>رقم الجوال</th>
                                    <th>القسم</th>
                                    <th>الايميل</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($group['students_data'] as $member)
                                    <tr>
                                        <td>{{isset($member['user_id']) ? $member['user_id'] : '-'}}</td>
                                        <td>{{isset($member['name']) ? $member['name'] : '-'}}
                                            @if($member['isLeader'])(قائد الفريق) @endif
                                        </td>
                                        <td>{{isset($member['mobile_number']) ? $member['mobile_number'] : '-'}}</td>
                                        <td>{{isset($member['department']) ? $member['department'] : '-'}}</td>
                                        <td>{{isset($member['email']) ? $member['email'] : '-'}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="row invoice-info">
                                <div class="invoice-col">
                                    <h4 class="mt-5 ">بيانات المشروع</h4>
                                    <address>
                                        <strong>العنوان المبدئي: </strong>
                                        {{isset($group['initialProjectTitle']) ? $group['initialProjectTitle'] : '-'}}
                                        <br>
                                        <strong>هل المجموعة ستتخرج بالفصل الأول: </strong>
                                        {{(isset($group['graduateInFirstSemester']) ? $group['graduateInFirstSemester'] : '-') == 0 ? 'لا' : 'نعم'}}
                                        <br>
                                        <strong>شكل المشروع: </strong>
                                        @if(isset($group['tags']))
                                            @foreach($group['tags'] as $tag)
                                                @if($loop->last) {{$tag}}.
                                                @else {{$tag}},
                                                @endif
                                            @endforeach
                                        @endif
                                        <br>
                                    </address>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection


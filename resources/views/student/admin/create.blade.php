@extends('layout')
@section('content')

    @if(isset($notifications) and $notifications != null)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">الإشعارات</h3>
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
                                    <td>{{$notification['message']}}</td>
                                    <td>
                                        <form
                                            action="{{route('student.admin.response',['from'=>$notification['from'],'to'=>$notification['to']])}}"
                                            method="post">
                                            @csrf
                                            <label>
                                                <input type="text" value="{{$key}}" name="notification_key" hidden>
                                            </label>
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

    @includeIf('student.group.members_form')

@endsection

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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">الاشعارات</h3>

                {{--                <div class="card-tools">--}}
                {{--                    <div class="input-group input-group-sm" style="width: 150px;">--}}
                {{--                        <input type="text" name="table_search" class="form-control float-right" placeholder="جستجو">--}}

                {{--                        <div class="input-group-append">--}}
                {{--                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
                {{--                </div>--}}
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>نوع الاشعار</th>
                        <th>العنوان المبدئي للمشروع</th>
                        <th>الرد</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($notifications as $notification)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$notification['from_name']}}</td>
                            <td>{{$notification['message']}}</td>
                            <td>{{$notification['initial_title']}}</td>
                            <td>
                                <form
                                    action="{{route('group.teacher.replyRequest',['from'=>$notification['from'],'to'=>$notification['to']])}}"
                                    method="post">
                                    @csrf
                                    <button class="btn btn-success" name="reply" value="accept">قبول</button>
                                    <button class="btn btn-danger" name="reply" value="reject">رفض</button>
                                    {{--                                    <input type="button" value="accept" name="reply" class="btn btn-success">--}}
                                    {{--                                    <input type="button" value="accept" name="reply" class="btn btn-success">--}}
                                </form>
                                {{--                                <a href="{{route('test',['data'=>'data'])}}">--}}
                                {{--                                </a>--}}
                                {{--                                <a href="{{route('test')}}">--}}
                                {{--                                    <button class="btn btn-danger">رفض</button>--}}
                                {{--                                </a>--}}
                            </td>

                            {{--                            <td>--}}
                            {{--                                <form action="{{route('group.teacher.acceptRequest')}}" method="post">--}}
                            {{--                                    <button type="submit"></button>--}}
                            {{--                                    <button type="submit"></button>--}}
                            {{--                                </form>--}}
                            {{--                            </td>--}}
                        </tr>
                    @endforeach
                    {{--                    <tr>--}}
                    {{--                        <td>۱۸۳</td>--}}
                    {{--                        <td>محمد</td>--}}
                    {{--                        <td>۱۱-۷-۲۰۱۴</td>--}}
                    {{--                        <td>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ</td>--}}
                    {{--                    </tr>--}}
                    {{--                    <tr>--}}
                    {{--                        <td>۲۱۹</td>--}}
                    {{--                        <td>محمدرضا</td>--}}
                    {{--                        <td>۱۱-۷-۲۰۱۴</td>--}}
                    {{--                        <td>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ</td>--}}
                    {{--                    </tr>--}}
                    {{--                    <tr>--}}
                    {{--                        <td>۶۵۷</td>--}}
                    {{--                        <td>رضا</td>--}}
                    {{--                        <td>۱۱-۷-۲۰۱۴</td>--}}
                    {{--                        <td><span class="badge badge-primary">تایید شده</span></td>--}}
                    {{--                        <td>لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ</td>--}}
                    {{--                    </tr>--}}
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>

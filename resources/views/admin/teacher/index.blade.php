@extends('layout')
@section('title', 'بيانات المدرسين')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">بيانات المشرفين</h3>
        </div>
        <div class="card-body">
            <table id="data-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th style="width: 68px">الرقم الوظيفي</th>
                    <th style="width: 220px;">الاسم</th>
                    {{--                    <th>الإيميل</th>--}}
                    <th style="width: 70px;">رقم الجوال</th>
                    <th style="width: 52px;">عدد المجموعات</th>
                    <th style="width: 250px;">الإعدادات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($teachers as $key => $teacher)
                    <tr>
                        <td @if(!isset($teacher['user_id']))
                            style="text-align: center" @endif>
                            {{isset($teacher['user_id']) ? $teacher['user_id'] : '-'}}
                        </td>
                        <td @if(!isset($teacher['name']))
                            style="text-align: center" @endif>
                            {{isset($teacher['name']) ? $teacher['name'] : '-'}}
                        </td>
                        {{--                        <td @if(!isset($teacher['email']))--}}
                        {{--                            style="text-align: center" @endif>--}}
                        {{--                            {{isset($teacher['email']) ? $teacher['email'] : '-'}}--}}
                        {{--                        </td>--}}
                        <td @if(!isset($teacher['mobile_number']))
                            style="text-align: center" @endif>
                            {{isset($teacher['mobile_number']) ? $teacher['mobile_number'] : '-'}}
                        </td>
                        <td style="text-align: center">
                            {{isset($teacher['groups_number']) ? $teacher['groups_number'] : '-'}}
                        </td>

                        <td class="project-actions text-right">
                            <a class="btn btn-info btn-sm" href="{{route('admin.teacher.show',['key'=>$key])}}">
                                <i class="fa fa-users">
                                </i>
                                المجموعات
                            </a>
                            <a class="btn btn-primary btn-sm" href="{{route('admin.teacher.edit',['key'=>$key])}}">
                                <i class="fa fa-pencil">
                                </i>
                                تعديل
                            </a>
                            {{--                                @if($teacher['role'] == 'leader')--}}
                            {{--                               class="btn btn-secondary btn-sm"--}}
                            {{--                               href="#"--}}
                            {{--                               @else--}}
                            {{--                               class="btn btn-warning btn-sm promotion"--}}
                            {{--                               href="{{route('leader.teacher.promotion',['key'=>$key])}}"--}}
                            {{--                               @endif--}}
                            {{--                               class="btn btn-secondary btn-sm"--}}
                            {{--                               href="#"--}}
                            {{--                               @else--}}
                            @if($teacher['role'] == 'teacher')
                                <a class="btn btn-warning btn-sm promotion"
                                   href="{{route('admin.teacher.promotion',['key'=>$key])}}"
                                   style="padding-right: 14px; padding-left: 15px;">
                                    <i class="fa fa-user-plus"></i>
                                    ترقية
                                </a>
                            @else
                                <a class="btn btn-warning btn-sm demotion"
                                   href="{{route('admin.teacher.demotion',['key'=>$key])}}">
                                    <i class="fa fa-user-times"></i>
                                    تخفيض
                                </a>
                            @endif
                            <a class="btn btn-danger btn-sm delete-confirm"
                               href="{{route('admin.teacher.destroy',['key'=>$key])}}">
                                <i class="fa fa-trash">
                                </i>
                                حذف
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>الرقم المدرس</th>
                    <th>الاسم</th>
                    {{--                    <th>الإيميل</th>--}}
                    <th>رقم الجوال</th>
                    <th>عدد المجموعات</th>
                    <th>الإعدادات</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(function () {
            $('.delete-confirm').on('click', function (event) {
                //get teacher name and put it in confirm alert
                var currentRow = $(this).closest("tr");
                var teacher_name = currentRow.find("td:eq(1)").text(); // get teacher name
                event.preventDefault();
                const url = $(this).attr('href');
                swal({
                    title: 'هل تريد حذف ' + teacher_name + '?',
                    text: 'هذا سيقوم بحذف المشرف نهائياً!',
                    icon: 'warning',
                    buttons: ["إلغاء", "نعم"],
                }).then(function (value) {
                    if (value) {
                        window.location.href = url;
                    }
                });
            });
            $('.promotion').on('click', function (event) {
                //get teacher name and put it in confirm alert
                var currentRow = $(this).closest("tr");
                var teacher_name = currentRow.find("td:eq(1)").text(); // get teacher name
                event.preventDefault();
                const url = $(this).attr('href');
                swal({
                    title: 'هل تريد ترقية ' + teacher_name + '?',
                    icon: 'warning',
                    buttons: ["إلغاء", "نعم"],
                }).then(function (value) {
                    if (value) {
                        window.location.href = url;
                    }
                });
                $('.demotion').on('click', function (event) {
                    //get teacher name and put it in confirm alert
                    var currentRow = $(this).closest("tr");
                    var teacher_name = currentRow.find("td:eq(1)").text(); // get teacher name
                    event.preventDefault();
                    const url = $(this).attr('href');
                    swal({
                        title: 'هل تريد تخفيض ' + teacher_name + '?',
                        icon: 'warning',
                        buttons: ["إلغاء", "نعم"],
                    }).then(function (value) {
                        if (value) {
                            window.location.href = url;
                        }
                    });
                });
            })
        });
    </script>
@endpush

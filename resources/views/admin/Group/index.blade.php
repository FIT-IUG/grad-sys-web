@extends('layout')
@section('title', 'title')
@section('content')
    {{--    <div class="card">--}}
    {{--        <div class="card-header">--}}
    {{--            <h3 class="card-title">الطلبة</h3>--}}

    {{--        </div>--}}
    {{--        <div class="card-body p-0">--}}
    {{--            <table class="table table-striped projects">--}}
    {{--                <thead>--}}
    {{--                <tr>--}}
    {{--                    <th>#</th>--}}
    {{--                    <th>اسم قائد الفريق</th>--}}
    {{--                    <th>الرقم الجامعي لقائد الفريق</th>--}}
    {{--                    <th>اسم مشرف الفريق</th>--}}
    {{--                    <th>عنوان المشروع</th>--}}
    {{--                    <th>الإعدادات</th>--}}
    {{--                </tr>--}}
    {{--                </thead>--}}
    {{--                <tbody>--}}
    {{--                @foreach($groups as $group)--}}
    {{--                    <tr>--}}
    {{--                        <td>{{$loop->iteration}}</td>--}}
    {{--                        <td @if(!isset($group['group_leader_data']))--}}
    {{--                            style="text-align: center" @endif>--}}
    {{--                            {{isset($group['group_leader_data']['name']) ? $group['group_leader_data']['name'] : '-'}}--}}
    {{--                        </td>--}}
    {{--                        <td @if(!isset($group['group_leader_data']))--}}
    {{--                            style="text-align: center" @endif>--}}
    {{--                            {{isset($group['group_leader_data']['user_id']) ? $group['group_leader_data']['user_id'] : '-'}}--}}
    {{--                        </td>--}}
    {{--                        <td @if(!isset($group['teacher_data']))--}}
    {{--                            style="text-align: center" @endif>--}}
    {{--                            {{isset($group['teacher_data']['name']) ? $group['teacher_data']['name'] : '-'}}--}}
    {{--                        </td>--}}
    {{--                        <td @if(!isset($group['project_data']['initialProjectTitle']))--}}
    {{--                            style="text-align: center" @endif>--}}
    {{--                            {{isset($group['project_data']['initialProjectTitle']) ? $group['project_data']['initialProjectTitle'] : '-'}}--}}
    {{--                        </td>--}}
    {{--                        <td class="project-actions text-right">--}}
    {{--                            <a class="btn btn-info btn-sm" href="#">--}}
    {{--                                <i class="fa fa-pencil">--}}
    {{--                                </i>--}}
    {{--                                تعديل--}}
    {{--                            </a>--}}
    {{--                            <a class="btn btn-danger btn-sm" href="#">--}}
    {{--                                <i class="fa fa-trash">--}}
    {{--                                </i>--}}
    {{--                                حذف--}}
    {{--                            </a>--}}
    {{--                        </td>--}}
    {{--                    </tr>--}}
    {{--                @endforeach--}}
    {{--                </tbody>--}}
    {{--            </table>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">المجموعات</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>اسم قائد الفريق</th>
{{--                    <th>الرقم الجامعي لقائد الفريق</th>--}}
                    <th>اسم المشرف</th>
                    <th>عنوان المشروع</th>
                    <th>الإعدادات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($groups as $key=>$group)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td @if(!isset($group['group_leader_data']))
                            style="text-align: center" @endif>
                            {{isset($group['group_leader_data']['name']) ? $group['group_leader_data']['name'] : '-'}}
                        </td>
{{--                        <td @if(!isset($group['group_leader_data']))--}}
{{--                            style="text-align: center" @endif>--}}
{{--                            {{isset($group['group_leader_data']['user_id']) ? $group['group_leader_data']['user_id'] : '-'}}--}}
{{--                        </td>--}}
                        <td @if(!isset($group['teacher_data']))
                            style="text-align: center" @endif>
                            {{isset($group['teacher_data']['name']) ? $group['teacher_data']['name'] : '-'}}
                        </td>
                        <td @if(!isset($group['project_data']['initialProjectTitle']))
                            style="text-align: center" @endif>
                            {{isset($group['project_data']['initialProjectTitle']) ? $group['project_data']['initialProjectTitle'] : '-'}}
                        </td>
                        <td class="project-actions text-right">
                            <a class="btn btn-info btn-sm" href="{{route('admin.group.edit',['group_key'=>$key])}}">
                                <i class="fa fa-pencil">
                                </i>
                                تعديل
                            </a>
                            <a class="btn btn-danger btn-sm delete-confirm"
                               href="{{route('admin.group.edit',['group_key'=>$key])}}">
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
                    <th>#</th>
                    <th>اسم قائد الفريق</th>
{{--                    <th>الرقم الجامعي لقائد الفريق</th>--}}
                    <th>اسم المشرف</th>
                    <th>عنوان المشروع</th>
                    <th>الإعدادات</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('.delete-confirm').on('click', function (event) {
            event.preventDefault();
            const url = $(this).attr('href');
            swal({
                title: 'هل تريد حذف المجموعة',
                text: 'هذا سيقوم بحذف المجموعة نهائياً!',
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


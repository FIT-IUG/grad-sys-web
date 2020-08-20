@extends('layout')
@section('title', 'بيانات المجموعات')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">المجموعات</h3>
        </div>
        <div class="card-body">
            <table id="data-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>اسم قائد الفريق</th>
                    <th>اسم المشرف</th>
                    <th>عنوان المشروع</th>
                    <th style="width: 145px">شكل المشروع</th>
                    <th style="width: 142px">الإعدادات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($groups as $key=>$group)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td @if(!isset($group['group_leader_data']))
                            style="text-align: center" @endif>
                            {{isset($group['group_leader_data']['name']) ? $group['group_leader_data']['name'] : 'لا يوجد'}}
                        </td>
                        <td @if(!isset($group['teacher_data']))
                            style="text-align: center" @endif>
                            {{isset($group['teacher_data']['name']) ? $group['teacher_data']['name'] : 'لا يوجد'}}
                        </td>
                        <td @if(!isset($group['project_data']['initialProjectTitle']))
                            style="text-align: center" @endif>
                            {{isset($group['project_data']['initialProjectTitle']) ? $group['project_data']['initialProjectTitle'] : 'لا يوجد'}}
                        </td>
                        <td @if(!isset($group['project_data']['tags']))
                            style="text-align: center" @endif>
                            @if(isset($group['project_data']['tags']) && $group['project_data']['tags']  != null)
                                @foreach($group['project_data']['tags'] as $tag)
                                    {{dd($tag)}}
                                    @if($loop->last) {{$tag}}.
                                    @else {{$tag}},
                                    @endif
                                @endforeach
                            @else
                                لا يوجد
                            @endif
                        </td>
                        <td class="project-actions text-right">
                            <a class="btn btn-info btn-sm" href="{{route('admin.group.edit',['group_key'=>$key])}}">
                                <i class="fa fa-pencil">
                                </i>
                                عرض المزيد
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
                    <th>اسم المشرف</th>
                    <th>عنوان المشروع</th>
                    <th>شكل المشروع</th>
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
                event.preventDefault();
                const url = $(this).attr('href');
                swal({
                    title: 'هل تريد حذف المجموعة حقًا؟',
                    text: 'هذا سيقوم بحذف المجموعة نهائيًا!',
                    icon: 'warning',
                    buttons: ["إلغاء", "نعم"],
                }).then(function (value) {
                    if (value) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
@endpush


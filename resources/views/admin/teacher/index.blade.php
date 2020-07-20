@extends('layout')
@section('title', 'title')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">بيانات المشرفين</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>الرقم المدرس</th>
                    <th>الاسم</th>
                    <th>الإيميل</th>
                    <th>رقم الجوال</th>
                    <th>القسم</th>
                    <th>الإعدادات</th>
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
                        <td @if(!isset($teacher['email']))
                            style="text-align: center" @endif>
                            {{isset($teacher['email']) ? $teacher['email'] : '-'}}
                        </td>
                        <td @if(!isset($teacher['mobile_number']))
                            style="text-align: center" @endif>
                            {{isset($teacher['mobile_number']) ? $teacher['mobile_number'] : '-'}}
                        </td>
                        <td style="text-align: center">
                            {{isset($teacher['department']) ? $teacher['department'] : '-'}}
                        </td>
                        <td class="project-actions text-right">
                            <a class="btn btn-primary btn-sm" href="{{route('admin.teacher.show',['key'=>$key])}}">
                                <i class="fa fa-users">
                                </i>
                                  المجموعات
                            </a>
                            <a class="btn btn-info btn-sm" href="{{route('admin.teacher.edit',['key'=>$key])}}">
                                <i class="fa fa-pencil">
                                </i>
                                تعديل
                            </a>
                            <a class="btn btn-danger btn-sm delete-confirm" href="{{route('admin.teacher.destroy',['key'=>$key])}}">
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
                    <th>الرقم الجامعي</th>
                    <th>الاسم</th>
                    <th>الإيميل</th>
                    <th>رقم الجوال</th>
                    <th>التخصص</th>
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
            var name = document.getElementsByClassName('delete-name');

            const url = $(this).attr('href');
            swal({
                title: 'هل تريد حذف المشرف',
                text: 'هذا سيقوم بحذف المشرف نهائياً!',
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

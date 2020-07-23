@extends('layout')
@section('title', 'بيانات الطلبة')
@section('content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">بيانات الطلبة</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="data-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>الرقم الجامعي</th>
                    <th>الاسم</th>
                    <th>الإيميل</th>
                    <th>رقم الجوال</th>
                    <th>التخصص</th>
                    <th>الإعدادات</th>
                </tr>
                </thead>
                <tbody>
                @foreach($students as $key => $student)
                    <tr>
                        <td @if(!isset($student['user_id']))
                            style="text-align: center" @endif>
                            {{isset($student['user_id']) ? $student['user_id'] : '-'}}
                        </td>
                        <td class="delete-name" @if(!isset($student['name']))
                            style="text-align: center" @endif>
                            {{isset($student['name']) ? $student['name'] : '-'}}
                        </td>
                        <td @if(!isset($student['email']))
                            style="text-align: center" @endif>
                            {{isset($student['email']) ? $student['email'] : '-'}}
                        </td>
                        <td @if(!isset($student['mobile_number']))
                            style="text-align: center" @endif>
                            {{isset($student['mobile_number']) ? $student['mobile_number'] : '-'}}
                        </td>
                        <td style="text-align: center">
                            {{isset($student['department']) ? $student['department'] : '-'}}
                        </td>
                        <td class="project-actions text-right">
                            <a class="btn btn-info btn-sm" href="{{route('admin.student.edit',['user_id'=>$key])}}">
                                <i class="fa fa-pencil">
                                </i>
                                تعديل
                            </a>

                            <a class="btn btn-danger btn-sm delete-confirm"
                               href="{{route('admin.student.destroy',['user_id'=>$key])}}">
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
                title: 'هل تريد حذف الطالب',
                text: 'هذا سيقوم بحذف الطالب نهائياً!',
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

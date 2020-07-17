@extends('layout')
@section('title','صفحة عضو الفريق الرئيسية')
@section('content')

    <div class="row">
        <h1>{{isset($message) ? $message : ''}}</h1>
    </div>

    @if(isset($group_data))
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
                                <th>#</th>
                                <th>الاسم</th>
                                <th>رقم الجوال</th>
                                <th>الايميل</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($group_data as $member)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{isset($member['name']) ? $member['name'] : '-' }}</td>
                                    <td>{{isset($member['mobile_number']) ? $member['mobile_number'] : '-' }}</td>
                                    <td>{{isset($member['email']) ? $member['email'] : '-' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

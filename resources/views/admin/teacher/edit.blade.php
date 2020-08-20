@extends('layout')
@section('title', 'تعديل بيانات مدرس')
@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">تعديل المشرف يدوياً</h3>
        </div>
        <div class="card-body">
            <form action="{{route('admin.teacher.update',['key' => request()->segment(5)])}}" method="POST">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>الاسم</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{$teacher['name']}}">
                        @error('name')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label>الرقم الوظيفي</label>
                        <input type="text" class="form-control @error('user_id') is-invalid  @enderror"
                               name="user_id" value="{{$teacher['user_id']}}">
                        @error('user_id')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>الإيميل</label>
                        <input type="email" class="form-control @error('email') is-invalid  @enderror"
                               name="email" value="{{$teacher['email']}}">
                        @error('email')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label>القسم</label>
                        <select class="form-control @error('department') is-invalid  @enderror" name="department">
                            <option value=""></option>
                            @foreach($departments as $department)
                                <option value="{{$department}}"
                                        @if($teacher['department'] == $department) selected @endif>{{$department}}</option>
                            @endforeach
                        </select>
                        @error('department')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>رقم الجوال</label>
                        <input type="text" class="form-control @error('mobile_number') is-invalid  @enderror"
                               name="mobile_number" value="{{$teacher['mobile_number']}}">
                        @error('mobile_number')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">تعديل</button>
            </form>
        </div>
    </div>
@endsection

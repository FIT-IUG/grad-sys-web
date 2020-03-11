@extends('layout')
@section('title','الصفحة الرئيسية')
@section('content')
    {{-- user manually registration --}}
    <div class="col-md-8">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">تسجيل طالب يدوياا</h3>
            </div>
            <div class="card-body">
                <form action="{{route('student.store')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputEmail1">الاسم</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="exampleInputEmail1" name="name" value="{{old('name')}}">
                        @error('name')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">الرقم الجامعي</label>
                        <input type="text" class="form-control @error('std') is-invalid  @enderror"
                               id="exampleInputEmail1" name="std" value="{{old('std')}}">
                        @error('std')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">الإيميل</label>
                        <input type="email" class="form-control @error('email') is-invalid  @enderror"
                               id="exampleInputEmail1" name="email" value="{{old('email')}}">
                        @error('email')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>التخصص</label>
                        <select class="form-control @error('department') is-invalid  @enderror" name="department">
                            <option value=""></option>
                            @foreach($departments as $department)
                                <option value="{{$department}}"
                                        @if(old('department') == $department) selected @endif>{{$department}}</option>
                            @endforeach
                        </select>
                        @error('department')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">رقم الجوال</label>
                        <input type="text" class="form-control @error('mobile_number') is-invalid  @enderror"
                               id="exampleInputEmail1" name="mobile_number" value="{{old('mobile_number')}}">
                        @error('mobile_number')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">تسجيل</button>
                </form>
            </div>
        </div>
    </div>
    {{-- export users from excel file --}}
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">إرسال ملف إكسل</h3>
            </div>
            <form role="form" method="POST" action="{{route('exportStudents')}}"
                  enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="exampleInputFile">ارسال ملف</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file"
                                       class="custom-file-input @error('excelFile') is-invalid @enderror"
                                       id="exampleInputFile" value="إستعراض" name="excelFile" required>
                                <label class="custom-file-label" for="exampleInputFile">اختار الملف</label>
                            </div>
                        </div>
                        @error('excelFile')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">ارسال</button>
                </div>
            </form>
        </div>
    </div>
@endsection

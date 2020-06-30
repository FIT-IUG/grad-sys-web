@extends('layout')
@section('content')
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">إستكمال إجراءات انشاء مجموعة</h3>
            </div>
            <div class="card-body">
                <form action="{{route('student.group.storeSupervisor')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>مشرف المجموعة</label>
                        <select class="form-control select2 select2-hidden-accessible" style="width: 100%;"
                                tabindex="-1" aria-hidden="true" dir="rtl" name="teacher">
                            <option value=""></option>
                            @foreach($teachers as $teacher)
                                <option value="{{$teacher['user_id']}}"
                                        @if(old('teacher') == $teacher['user_id']) selected @endif>{{$teacher['name']}}
                                </option>
                            @endforeach
                        </select>
                        @error('teacher')
                        <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <div class="form-group">
                            <label for="">العنوان المبدئي للمشروع</label>
                            <input type="text" class="form-control @error('initialProjectTitle') is-invalid @enderror"
                                   id="exampleInputEmail1" name="initialProjectTitle"
                                   value="{{old('initialProjectTitle')}}">
                            @error('initialProjectTitle')
                            <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>نوع المشروع </label>
                            <h6 style="font-size: small">
                                إذا كان المشروع يحتوي على اكثر من نوع, يمكن اختيار أكثر من إختيار.
                            </h6>
                            <select class="form-control select2 select2-hidden-accessible" multiple=""
                                    name="tags[]"
                                    style="width: 100%;text-align: right;direction: rtl" tabindex="-1"
                                    aria-hidden="true">
                                <option></option>
                                @foreach($tags as $tag)
                                    <option value="{{$tag}}"
                                            {{--                                        Becouse there is multiple select it should be like this--}}
                                            {{--                                        to get old selectd options--}}
                                            @if(old('tags')[0] != null)
                                            @foreach(old('tags') as $oldDepartment)
                                            @if($oldDepartment == $tag ) selected @endif
                                        @endforeach
                                        @endif> {{$tag}}
                                    </option>
                                @endforeach
                            </select>
                            {{--                        <h6></h6>--}}
                            @error('tags')
                            <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">تسجيل</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@extends('layout')
@section('title','الإعدادات')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">إعدادات النظام</h3>
                </div>
                <form role="form" method="post" action="{{route('admin.settings.update')}}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">الحد الأقصى لعدد أعضاء الفريق</label>
                                <input type="text"
                                       class="form-control @error('max_group_members') is-invalid  @enderror"
                                       name="max_group_members"
                                       value="{{$settings['max_group_members']}}">
                                @error('max_group_members')
                                <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">الحد الأدنى لعدد أعضاء الفريق</label>
                                <input type="text"
                                       class="form-control @error('min_group_members') is-invalid  @enderror"
                                       name="min_group_members"
                                       value="{{$settings['min_group_members']}}">
                                @error('min_group_members')
                                <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group col-md-3">
                                <label for="exampleInputEmail1">الحد الأقصى لعدد المجموعات للمشرف</label>
                                <input type="text"
                                       class="form-control @error('max_teacher_groups') is-invalid  @enderror"
                                       name="max_teacher_groups"
                                       value="{{$settings['max_teacher_groups']}}">
                                @error('max_teacher_groups')
                                <div class="alert alert-danger" style="margin-top: 10px">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

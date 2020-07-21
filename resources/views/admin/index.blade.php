@extends('layout')
@section('title','الصفحة الرئيسية')
@section('small_box')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{isset($statistics['number_of_students']) ? $statistics['number_of_students'] : '-'}}</h3>
                    <p>عدد الطلبة</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{isset($statistics['number_of_groups']) ? $statistics['number_of_groups'] : '-'}}</h3>
                    <p>عدد المجموعات</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{isset($statistics['number_of_teamed_students']) ? $statistics['number_of_teamed_students'] : '-'}}</h3>
                    <p>عدد الطلاب المتواجدين في فريق </p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{isset($statistics['number_of_teachers']) ? $statistics['number_of_teachers'] : '-'}}</h3>
                    <p>عدد المشرفين</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @includeIf('admin.create_student_form')
    @includeIf('teacher.teacherGroups')
    @includeIf('teacher.teacherNotifications')
    @includeIf('admin.exports_form')
@endsection


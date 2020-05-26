@extends('layout')
@section('title','الصفحة الرئيسية')
@section('small_box')
    <!-- Small boxes (Stat box) -->
    @if(hasRole('admin'))
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{$statistics['number_of_students']}}</h3>
                        <p>عدد الطلبة</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{$statistics['number_of_groups']}}</h3>
                        <p>عدد المجموعات</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{$statistics['number_of_teamed_students']}}</h3>
                        <p>عدد الطلاب المتواجدين في فريق </p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>1234</h3>
                        <p>عدد شيء ما</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('content')
    @if(hasRole('admin'))
        @includeIf('admin.dashboardForm')
    @endif
    @if(hasRole('student'))
        @if(!inGroup())
            @includeIf('student.group.members_form')
        @elseif(isGroupLeader() && !
isTeacherHasNotification())<!--  -->
        @includeIf('student.group.supervisor_initial_title_form')
        @elseif(isTeacherAccept() == null)
            <h1>انتظر رد المشرف على رسالتك.</h1>
        @elseif(isTeacherAccept())
            <h1>المدرس وافق على طلب ان يكون مشرف مجموعتك</h1>
        @elseif(!isTeacherAccept())
            <h1>رفض المدرس طلب ان يكون مشرف مجموعتك</h1>
        @endif
    @endif
    @if(hasRole('teacher'))
        @includeIf('teacher.index')
    @endif
@endsection

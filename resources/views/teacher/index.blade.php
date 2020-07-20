@extends('layout')
@section('title','الصفحة المدرس الرئيسية')
@section('content')
   @includeIf('teacher.teacherNotifications')
   @includeIf('teacher.teacherGroups')
@endsection


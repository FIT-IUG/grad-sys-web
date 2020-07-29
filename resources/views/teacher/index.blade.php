@extends('layout')
@section('title','صفحة المدرس الرئيسية')
@section('content')
   @includeIf('teacher.teacherNotifications')
   @includeIf('teacher.teacherGroups')
@endsection


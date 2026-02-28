@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.enrollments.course-enrollments', ['active_menu_id' => $menu_id,'course_id'=>$course_id,'student_id'=>$student_id])

@endsection


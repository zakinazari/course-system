@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.enrollments.special-course-list', ['active_menu_id' => $menu_id,'student_id'=>$student_id])

@endsection

@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.course-waiting.waiting-student-comment-report', ['active_menu_id' => $menu_id])

@endsection
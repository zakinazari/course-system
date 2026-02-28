@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.enrollments.course-list', ['active_menu_id' => $menu_id])

@endsection


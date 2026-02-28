@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.course-waiting.waiting-students', ['active_menu_id' => $menu_id])

@endsection

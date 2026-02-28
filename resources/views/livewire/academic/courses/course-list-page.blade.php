@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.courses.course-list', ['active_menu_id' => $menu_id])

@endsection

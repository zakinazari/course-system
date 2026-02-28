@extends('layouts/layoutMaster')

@section('content')

@livewire('assessment.attendance.student-attendance-list', ['active_menu_id' => $menu_id])

@endsection

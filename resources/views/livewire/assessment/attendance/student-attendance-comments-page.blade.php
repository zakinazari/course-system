@extends('layouts/layoutMaster')

@section('content')

@livewire('assessment.attendance.student-attendance-comments', ['active_menu_id' => $menu_id])

@endsection

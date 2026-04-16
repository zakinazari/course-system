@extends('layouts/layoutMaster')

@section('content')

@livewire('assessment.exam-attendance.exam-attendance-list', ['active_menu_id' => $menu_id])

@endsection


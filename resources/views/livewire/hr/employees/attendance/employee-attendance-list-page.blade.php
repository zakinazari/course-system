@extends('layouts/layoutMaster')

@section('content')

@livewire('hr.employees.attendance.employee-attendance-list', ['active_menu_id' => $menu_id])

@endsection

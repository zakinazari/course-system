@extends('layouts/layoutMaster')

@section('content')

@livewire('hr.employees.employee-list', ['active_menu_id' => $menu_id])

@endsection

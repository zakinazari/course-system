@extends('layouts/layoutMaster')

@section('content')

@livewire('hr.employees.employee-profile', ['active_menu_id' => $menu_id,'employee_id'=>$employee_id])

@endsection

@extends('layouts/layoutMaster')

@section('content')

@livewire('hr.payrolls.permanent-payroll-list', ['active_menu_id' => $menu_id])

@endsection
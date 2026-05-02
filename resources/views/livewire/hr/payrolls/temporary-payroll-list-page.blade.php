@extends('layouts/layoutMaster')

@section('content')

@livewire('hr.payrolls.temporary-payroll-list', ['active_menu_id' => $menu_id])

@endsection
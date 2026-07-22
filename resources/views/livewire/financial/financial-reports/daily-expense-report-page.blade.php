@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.financial-reports.daily-expense-report', ['active_menu_id' => $menu_id])

@endsection
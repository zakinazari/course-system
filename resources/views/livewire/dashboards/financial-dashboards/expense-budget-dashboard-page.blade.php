@extends('layouts/layoutMaster')

@section('content')

@livewire('dashboards.financial-dashboards.expense-budget-dashboard', ['active_menu_id' => $menu_id])

@endsection


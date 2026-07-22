@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.financial-reports.daily-income-report', ['active_menu_id' => $menu_id])

@endsection
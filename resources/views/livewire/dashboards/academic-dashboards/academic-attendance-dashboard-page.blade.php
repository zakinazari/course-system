@extends('layouts/layoutMaster')

@section('content')

@livewire('dashboards.academic-dashboards.academic-attendance-dashboard', ['active_menu_id' => $menu_id])


@endsection


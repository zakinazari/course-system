@extends('layouts/layoutMaster')

@section('content')

@livewire('dashboards.academic-dashboards.academic-results-dashboard', ['active_menu_id' => $menu_id])


@endsection


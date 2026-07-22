@extends('layouts/layoutMaster')

@section('content')

@livewire('dashboards.academic-dashboards.active-students-dashboard', ['active_menu_id' => $menu_id])


@endsection
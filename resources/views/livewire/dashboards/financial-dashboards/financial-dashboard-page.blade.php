@extends('layouts/layoutMaster')

@section('content')

@livewire('dashboards.financial-dashboards.financial-dashboard', ['active_menu_id' => $menu_id])

@endsection

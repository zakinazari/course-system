@extends('layouts/layoutMaster')

@section('content')

@livewire('dashboards.financial-dashboards.book-inventory-dashboard', ['active_menu_id' => $menu_id])

@endsection

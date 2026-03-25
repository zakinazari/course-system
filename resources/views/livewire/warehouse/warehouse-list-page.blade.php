@extends('layouts/layoutMaster')

@section('content')

@livewire('warehouse.warehouse-list', ['active_menu_id' => $menu_id])

@endsection

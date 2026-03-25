@extends('layouts/layoutMaster')

@section('content')

@livewire('warehouse.book-inventory.book-inventory-list', ['active_menu_id' => $menu_id])

@endsection
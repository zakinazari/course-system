@extends('layouts/layoutMaster')

@section('content')

@livewire('warehouse.book-inventory.book-purchase-list', ['active_menu_id' => $menu_id])

@endsection
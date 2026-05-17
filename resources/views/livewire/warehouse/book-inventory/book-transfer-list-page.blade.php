@extends('layouts/layoutMaster')

@section('content')

@livewire('warehouse.book-inventory.book-transfer-list', ['active_menu_id' => $menu_id])

@endsection
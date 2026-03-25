@extends('layouts/layoutMaster')

@section('content')

@livewire('warehouse.category.warehouse-category-list', ['active_menu_id' => $menu_id])

@endsection

@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.assets.asset-category-list', ['active_menu_id' => $menu_id])

@endsection


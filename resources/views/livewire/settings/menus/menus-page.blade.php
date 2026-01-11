
@extends('layouts/layoutMaster')

@section('content')

@livewire('settings.menus.menu-list', ['active_menu_id' => $menu_id])

@endsection

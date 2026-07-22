@extends('layouts/layoutMaster')

@section('content')

@livewire('settings.notification-category-role.notification-category-role-list', ['active_menu_id' => $menu_id])

@endsection

@extends('layouts/layoutMaster')

@section('content')

@livewire('settings.access-roles.access-role-list', ['active_menu_id' => $menu_id])

@endsection
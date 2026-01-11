@extends('layouts/layoutMaster')

@section('content')

@livewire('settings.permissions.permission-list', ['active_menu_id' => $menu_id])

@endsection

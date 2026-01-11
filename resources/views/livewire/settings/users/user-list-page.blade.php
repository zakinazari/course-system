@extends('layouts/layoutMaster')

@section('content')

@livewire('settings.users.user-list', ['active_menu_id' => $menu_id])

@endsection
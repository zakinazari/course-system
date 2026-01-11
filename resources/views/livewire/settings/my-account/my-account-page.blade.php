@extends('layouts/layoutMaster')

@section('content')

@livewire('settings.my-account.change-profile', ['active_menu_id' => $menu_id])
@endsection

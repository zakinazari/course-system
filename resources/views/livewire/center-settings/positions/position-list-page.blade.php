@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.positions.position-list', ['active_menu_id' => $menu_id])

@endsection

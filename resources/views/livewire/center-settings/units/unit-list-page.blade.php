@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.units.unit-list', ['active_menu_id' => $menu_id])

@endsection
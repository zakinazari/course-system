@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.placement-test-settings.placement-test-settings-list', ['active_menu_id' => $menu_id])

@endsection
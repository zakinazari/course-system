@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.times.time-list', ['active_menu_id' => $menu_id])

@endsection

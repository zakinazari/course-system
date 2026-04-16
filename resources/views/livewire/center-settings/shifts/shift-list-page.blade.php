@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.shifts.shift-list', ['active_menu_id' => $menu_id])

@endsection


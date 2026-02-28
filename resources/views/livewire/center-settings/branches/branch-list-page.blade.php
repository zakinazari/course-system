@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.branches.branch-list', ['active_menu_id' => $menu_id])

@endsection
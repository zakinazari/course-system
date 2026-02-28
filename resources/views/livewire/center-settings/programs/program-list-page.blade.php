@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.programs.program-list', ['active_menu_id' => $menu_id])

@endsection
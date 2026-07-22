@extends('layouts/layoutMaster')

@section('content')

@livewire('logs.system-logs.system-log-list', ['active_menu_id' => $menu_id])

@endsection

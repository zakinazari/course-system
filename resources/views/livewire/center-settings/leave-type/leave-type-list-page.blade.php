@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.leave-type.leave-type-list', ['active_menu_id' => $menu_id])

@endsection

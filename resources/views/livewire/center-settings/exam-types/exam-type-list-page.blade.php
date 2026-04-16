@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.exam-types.exam-type-list', ['active_menu_id' => $menu_id])

@endsection


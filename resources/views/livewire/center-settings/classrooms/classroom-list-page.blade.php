@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.classrooms.classroom-list', ['active_menu_id' => $menu_id])

@endsection


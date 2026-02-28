@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.meetings.meeting-list', ['active_menu_id' => $menu_id])

@endsection
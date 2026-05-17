@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.sections.section-list', ['active_menu_id' => $menu_id])

@endsection
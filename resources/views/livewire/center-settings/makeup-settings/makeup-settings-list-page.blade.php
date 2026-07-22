@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.makeup-settings.makeup-setting-list', ['active_menu_id' => $menu_id])

@endsection


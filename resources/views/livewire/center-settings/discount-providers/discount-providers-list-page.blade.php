@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.discount-providers.discount-provider-list', ['active_menu_id' => $menu_id])

@endsection

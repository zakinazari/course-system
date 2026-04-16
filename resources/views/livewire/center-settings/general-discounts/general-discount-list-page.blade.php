@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.general-discounts.general-discount-list', ['active_menu_id' => $menu_id])

@endsection
@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.fee-types.fee-type-list', ['active_menu_id' => $menu_id])

@endsection

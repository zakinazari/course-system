@extends('layouts/layoutMaster')

@section('content')

@livewire('hr.contracts.temporary-contract-list', ['active_menu_id' => $menu_id])

@endsection
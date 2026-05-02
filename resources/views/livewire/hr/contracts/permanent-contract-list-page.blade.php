@extends('layouts/layoutMaster')

@section('content')

@livewire('hr.contracts.permanent-contract-list', ['active_menu_id' => $menu_id])

@endsection

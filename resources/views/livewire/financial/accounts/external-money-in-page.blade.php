@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.accounts.external-money-in', ['active_menu_id' => $menu_id])

@endsection

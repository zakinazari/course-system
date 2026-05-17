@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.accounts.account-list', ['active_menu_id' => $menu_id])

@endsection
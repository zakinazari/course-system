@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.accounts.account-transfer', ['active_menu_id' => $menu_id])

@endsection

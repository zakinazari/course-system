@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.expenses.expense-list', ['active_menu_id' => $menu_id])

@endsection

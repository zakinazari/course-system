@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.expenses.expense-category-list', ['active_menu_id' => $menu_id])

@endsection

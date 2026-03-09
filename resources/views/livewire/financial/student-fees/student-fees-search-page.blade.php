@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.student-fees.student-fees-search', ['active_menu_id' => $menu_id])

@endsection

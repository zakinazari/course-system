@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.student-fees.reports.student-fees-report', ['active_menu_id' => $menu_id])

@endsection

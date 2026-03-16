@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.student-fees.reports.course-fees-discount-report', ['active_menu_id' => $menu_id])

@endsection
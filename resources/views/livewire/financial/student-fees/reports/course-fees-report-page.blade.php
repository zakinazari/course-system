@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.student-fees.reports.course-fees-report', ['active_menu_id' => $menu_id])

@endsection
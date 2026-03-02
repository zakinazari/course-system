@extends('layouts/layoutMaster')

@section('content')

@livewire('assessment.mark-entry.student-course-result-entry', ['active_menu_id' => $menu_id])

@endsection
@extends('layouts/layoutMaster')

@section('content')

@livewire('assessment.student-result.student-course-result-list', ['active_menu_id' => $menu_id])

@endsection

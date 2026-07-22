@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.student-activities.activity-category-list', ['active_menu_id' => $menu_id])

@endsection



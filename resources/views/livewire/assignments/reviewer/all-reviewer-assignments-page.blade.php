@extends('layouts/layoutMaster')

@section('content')

@livewire('assignments.reviewer.all-reviewer-assignments', ['active_menu_id' => $menu_id])

@endsection

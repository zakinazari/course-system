@extends('layouts/layoutMaster')

@section('content')

@livewire('assignments.reviewer.reviewer-assignment-view', ['active_menu_id' => $menu_id,'review_id'=>$review_id])

@endsection

@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.students.student-list', ['active_menu_id' => $menu_id])

@endsection

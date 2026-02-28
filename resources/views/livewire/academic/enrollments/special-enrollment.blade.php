@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.enrollments.special-enrollments', ['active_menu_id' => $menu_id,'student_id'=>$student_id])

@endsection

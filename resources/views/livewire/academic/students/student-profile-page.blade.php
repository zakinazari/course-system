@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.students.student-profile', ['active_menu_id' => $menu_id,'student_id'=>$student_id])

@endsection

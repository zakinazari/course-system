@extends('layouts/layoutMaster')

@section('content')

@livewire('financial.student-fees.student-financial-profile', ['active_menu_id' => $menu_id,'student_id'=>$student_id])

@endsection
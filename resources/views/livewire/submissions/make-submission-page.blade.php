@extends('layouts/layoutMaster')

@section('content')

@livewire('submissions.make-submission', ['active_menu_id' => $menu_id,'submission_id'=>$submission_id])

@endsection
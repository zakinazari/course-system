@extends('layouts/layoutMaster')

@section('content')
@livewire('submissions.submission-view', ['active_menu_id' => $menu_id,'submission_id'=>$submission_id])

@endsection








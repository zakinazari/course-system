@extends('layouts/layoutMaster')

@section('content')

@livewire('submissions.submission-list', ['active_menu_id' => $menu_id])

@endsection
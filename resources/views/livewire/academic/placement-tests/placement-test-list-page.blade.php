@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.placement-tests.placement-test-list', ['active_menu_id' => $menu_id])

@endsection

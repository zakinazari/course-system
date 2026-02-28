@extends('layouts/layoutMaster')

@section('content')

@livewire('academic.visitors.visitor-list', ['active_menu_id' => $menu_id])

@endsection
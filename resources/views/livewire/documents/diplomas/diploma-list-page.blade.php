@extends('layouts/layoutMaster')

@section('content')

@livewire('documents.diplomas.diploma-list', ['active_menu_id' => $menu_id])

@endsection

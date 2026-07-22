@extends('layouts/layoutMaster')

@section('content')

@livewire('documents.result-cards.result-card-list', ['active_menu_id' => $menu_id])

@endsection
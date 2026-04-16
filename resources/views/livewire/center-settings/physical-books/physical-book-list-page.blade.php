@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.physical-books.physical-book-list', ['active_menu_id' => $menu_id])

@endsection

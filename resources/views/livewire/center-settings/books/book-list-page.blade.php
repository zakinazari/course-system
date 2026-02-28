@extends('layouts/layoutMaster')

@section('content')

@livewire('center-settings.books.book-list', ['active_menu_id' => $menu_id])

@endsection

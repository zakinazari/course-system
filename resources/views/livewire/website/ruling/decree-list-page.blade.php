<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.ruling.decree-list', ['active_menu_id' => $menu_id])

    @endsection
</div>
<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.ruling.order-list', ['active_menu_id' => $menu_id])

    @endsection
</div>
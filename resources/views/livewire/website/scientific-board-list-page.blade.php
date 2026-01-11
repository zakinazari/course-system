<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.scientific-board-list', ['active_menu_id' => $menu_id])

    @endsection
</div>

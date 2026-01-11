<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.leadership-board-list', ['active_menu_id' => $menu_id])

    @endsection
</div>

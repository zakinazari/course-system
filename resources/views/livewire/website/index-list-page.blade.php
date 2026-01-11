<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.index-list', ['active_menu_id' => $menu_id])

    @endsection
</div>

<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.post-list', ['active_menu_id' => $menu_id])

    @endsection
</div>


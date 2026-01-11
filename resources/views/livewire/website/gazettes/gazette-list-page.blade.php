<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.gazettes.gazette-list', ['active_menu_id' => $menu_id])

    @endsection
</div>

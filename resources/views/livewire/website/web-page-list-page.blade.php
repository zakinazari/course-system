<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.web-page-list', ['active_menu_id' => $menu_id])

    @endsection
</div>

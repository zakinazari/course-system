<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.about-us.about-us-list', ['active_menu_id' => $menu_id])

    @endsection
</div>

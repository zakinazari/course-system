<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.abstract.accepted-abstract-list', ['active_menu_id' => $menu_id])

    @endsection
</div>
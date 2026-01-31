<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('website.axes.sub-axis-list', ['active_menu_id' => $menu_id])

    @endsection
</div>
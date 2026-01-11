<div>
   @extends('layouts/layoutMaster')
    @section('content')

    @livewire('issues.issue-list', ['active_menu_id' => $menu_id])

    @endsection
</div>

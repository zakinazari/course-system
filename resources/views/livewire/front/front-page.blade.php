@extends('front-layouts/front-master')
@section('content')
@livewire('front.custom-page', ['active_menu_id' => $menu_id,'slug'=>$slug])
@endsection


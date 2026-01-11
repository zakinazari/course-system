@extends('front-layouts/front-master')
@section('content')
@livewire('front.article',['active_menu_id' =>$menu_id,'submission_id'=>$submission_id])
@endsection

@extends('front-layouts/front-master')
@section('content')
@livewire('front.single-post', ['slug' => $slug,'post_id'=>$post_id])
@endsection

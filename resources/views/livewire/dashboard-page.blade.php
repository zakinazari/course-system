@extends('layouts/layoutMaster')

@section('content')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection
@section('page-style')
 <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-logistics-dashboard.css')}}" />
@endsection

@livewire('dashboard', ['active_menu_id' => $menu_id])

@section('vendor-script')
<!-- form-editor -->
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection
@section('page-script')
<!-- form-vizard -->
 <script src="{{asset('assets/js/app-academy-dashboard.js')}}" defer></script>
<script src="{{asset('assets/js/app-logistics-dashboard.js')}}" defer></script>
@endsection
@endsection

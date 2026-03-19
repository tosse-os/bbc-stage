@extends('layouts.dashboard')

@section('content')

@php
$state = dashboard_access_state(get_current_user_id());
@endphp

@if ($state === 'payment_required')
@include('dashboard.partials.payment-required')
@else
@include('dashboard.analyses.index')
@endif

@endsection

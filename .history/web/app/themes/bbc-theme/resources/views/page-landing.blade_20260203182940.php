{{--
Template Name: Landing Page
--}}
@extends('layouts.landingpage')

@section('content')
@include('partials.landing.hero')
@include('partials.landing.about')
@include('partials.landing.contact')
@include('partials.landing.market-insights')
@endsection

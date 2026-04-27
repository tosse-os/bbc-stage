{{--
Template Name: Landing Page
--}}
@extends('layouts.landingpage')

@section('content')
@include('partials.landing.hero')
@include('partials.landing.market-insights')
@include('partials.landing.about')
@include('partials.landing.team')
@include('partials.landing.cta')
@include('partials.landing.contact')
@endsection

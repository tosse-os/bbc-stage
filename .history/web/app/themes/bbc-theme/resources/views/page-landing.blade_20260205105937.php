{{--
Template Name: Landing Page
--}}
@extends('layouts.landingpage')

@section('content')
@include('partials.landing.hero')
@include('partials.landing.about')
<div class="pattern-wrapper">
  @include('partials.landing.contact')
  @include('partials.landing.team')
  @include('partials.landing.market-insights')
</div>

  @endsection

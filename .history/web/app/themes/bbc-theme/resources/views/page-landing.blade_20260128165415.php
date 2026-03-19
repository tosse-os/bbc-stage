{{--
Template Name: Landing Page
--}}
@extends('layouts.app')

@section('content')
@include('partials.landing.hero')
@include('partials.landing.about')
@include('partials.landing.market-insights')
@endsection

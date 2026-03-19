{{--
Template Name: Conversion – Trial
--}}

@extends('layouts.conversion')

@section('content')

<section class="relative min-h-screen overflow-hidden">

  <div class="absolute inset-0">
    <img
      src="{{ Vite::asset('resources/landingpage/images/hero-bg.jpg') }}"
      alt=""
      class="h-full w-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/20 via-slate-950/30 to-slate-950/10"></div>
  </div>

  <div class="relative">
    <div class="container-content pt-32 pb-40">

      <div class="grid grid-cols-1 gap-x-16 gap-y-20 lg:grid-cols-[1.1fr_0.9fr] lg:items-end">

        {{-- LEFT · HEADLINE --}}
        <div>
          <h1 class="text-4xl font-semibold tracking-tight text-white lg:text-5xl">
            {!! pll__('Start Your') !!}
            <span class="text-brand-primary">
              {!! pll__('Free Trial') !!}
            </span>
          </h1>

          <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-300">
            {!! pll__('Sign up to access advanced trading signals and structured market analyses. Cancel anytime during your free trial.') !!}
          </p>
        </div>

        {{-- RIGHT · FORM --}}
        <div class="lg:row-span-2">
          @include('partials.auth.signup-form')
        </div>

        {{-- LEFT · PLAN CARD --}}
        <div class="max-w-md">
          @include('partials.subscription.plan-card')
        </div>

      </div>

    </div>
  </div>

  <div class="relative">
    @include('sections.conversion-footer')
  </div>

</section>

@endsection

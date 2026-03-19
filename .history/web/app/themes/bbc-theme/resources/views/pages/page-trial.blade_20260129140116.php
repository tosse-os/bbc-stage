@extends('layouts.app')

@section('content')
<section class="relative min-h-screen overflow-hidden">

  <div class="absolute inset-0">
    <img
      src="{{ Vite::asset('resources/images/hero-bg.jpg') }}"
      alt=""
      class="h-full w-full object-cover opacity-80">
    <div class="absolute inset-0 bg-gradient-to-b from-slate-950/80 via-slate-950/70 to-slate-950/90"></div>
  </div>

  <div class="relative">
    <div class="container-content pt-32 pb-40">

      <h1 class="text-4xl font-semibold tracking-tight text-white lg:text-5xl">
        Start Your <span class="text-brand-primary">Free Trial</span>
      </h1>

      <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-300">
        Sign up to access advanced trading signals and structured market analyses.
        Cancel anytime during your free trial.
      </p>

      <div class="mt-20 grid grid-cols-1 items-start gap-16 lg:grid-cols-[1.1fr_0.9fr]">
        @include('partials.subscription.plan-card')
        @include('partials.auth.signup-form')
      </div>

    </div>
  </div>

</section>
@endsection

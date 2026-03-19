{{--
Template Name: Standard Page (Legal Pages)
--}}

@extends('layouts.landingpage')

@section('content')
<section class="relative overflow-hidden py-24 scroll-mt-20">

  {{-- Background --}}
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-standard"></div>
    <div class="absolute inset-0 standard-pattern pattern-section"></div>
  </div>

  {{-- Content --}}
  <div class="relative container-content max-w-3xl">

    {{-- Page Title --}}
    <header class="mb-10">
      <h1 class="text-3xl font-semibold tracking-tight text-brand-primaryFontDark">
        {!! get_the_title() !!}
      </h1>
    </header>

    {{-- Page Content --}}
    <article class="prose prose-slate max-w-none">
      {!! the_content() !!}
    </article>

  </div>

</section>
@endsection

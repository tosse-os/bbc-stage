{{--
Template Name: Standard Page (Legal Pages)
--}}

@extends('layouts.landingpage')

@section('content')
<main class="py-24">
  <div class="container-content max-w-3xl">

    {{-- Page Title --}}
    <header class="mb-10">
      <h1 class="text-3xl font-semibold tracking-tight text-brand-primaryFontDark">
        {!! get_the_title() !!}
      </h1>
    </header>

    {{-- Page Content --}}
    <article class="prose prose-slate max-w-none">
      <section id="standard-page" class="relative overflow-hidden py-20 lg:py-28 scroll-mt-15">
        <div class="absolute inset-0 -z-10">
          <div class="absolute inset-0 bg-team"></div>
          <div class="absolute inset-0 team-pattern pattern-section"></div>
        </div>
        {!! the_content() !!}
        </section>
    </article>

  </div>
</main>
@endsection

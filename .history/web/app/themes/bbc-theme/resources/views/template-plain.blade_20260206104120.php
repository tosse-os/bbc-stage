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
      {!! the_content !!}
    </article>

  </div>
</main>
@endsection

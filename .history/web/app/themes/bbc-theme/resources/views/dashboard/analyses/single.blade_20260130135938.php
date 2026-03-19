@extends('layouts.dashboard')

@section('content')

<a href="/dashboard"
  class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 mb-6">
  ← Zur Übersicht
</a>

<article class="max-w-5xl mx-auto space-y-10">

  <header class="space-y-3">
    <h1 class="text-3xl font-semibold">
      {{ get_the_title() }}
    </h1>

    <div class="text-sm text-dashboard-muted flex flex-wrap gap-x-4 gap-y-1">
      <span>
        {{ get_field('market') }}
      </span>

      <span>
        {{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}
      </span>
    </div>
  </header>

  @if ($image = get_field('chart_image'))
  <div class="relative">

    <img
      src="{{ $image['url'] }}"
      alt=""
      class="w-full rounded-2xl shadow-lg cursor-zoom-in"
      data-chart-zoom>

    <p class="text-xs text-dashboard-muted mt-2">
      Klick auf die Grafik zum Vergrößern
    </p>

  </div>
  @endif

  <div class="prose prose-lg max-w-none">
    {!! get_field('content_text') !!}
  </div>

</article>

@endsection

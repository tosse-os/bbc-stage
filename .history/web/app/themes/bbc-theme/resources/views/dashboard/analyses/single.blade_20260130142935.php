@extends('layouts.dashboard')

@section('content')

<article class="max-w-5xl mx-auto space-y-10">

  <header class="flex flex-col gap-4">

    <div class="flex items-center gap-4 text-sm">
      <a
        href="/dashboard"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-slate-100 text-slate-600 hover:bg-slate-200 hover:text-slate-900 transition">
        ← Übersicht
      </a>
    </div>

    <div class="space-y-2">
      <h1 class="text-3xl font-semibold">
        {{ get_the_title() }}
      </h1>

      <div class="text-sm text-dashboard-muted flex flex-wrap gap-x-4 gap-y-1">
        <span>{{ get_field('market') }}</span>
        <span>{{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}</span>
      </div>
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

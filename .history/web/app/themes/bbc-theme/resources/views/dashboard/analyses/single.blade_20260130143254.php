@extends('layouts.dashboard')

@section('content')

<article class="max-w-6xl mx-auto space-y-8">

  <header class="flex items-start justify-between gap-6">

    <div class="space-y-1">
      <h1 class="text-3xl font-semibold leading-tight">
        {{ get_the_title() }}
      </h1>

      <div class="flex items-center gap-3 text-sm text-dashboard-muted">
        <span>{{ get_field('market') }}</span>
        <span>·</span>
        <span>{{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}</span>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <a
        href="/dashboard"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition text-sm whitespace-nowrap">
        ← Übersicht
      </a>
    </div>

  </header>

  @if ($image = get_field('chart_image'))
  <div class="relative">

    <img
      src="{{ $image['url'] }}"
      alt=""
      class="w-full rounded-2xl shadow-lg cursor-zoom-in"
      data-chart-zoom>

  </div>
  @endif

  <div class="prose prose-lg max-w-none">
    {!! get_field('content_text') !!}
  </div>

</article>

@endsection

@extends('layouts.dashboard')

@section('content')

<article class="max-w-6xl mx-auto space-y-8">

  <header class="bg-white/80 backdrop-blur rounded-xl px-6 py-4 flex items-start justify-between gap-6 shadow-sm">

    <div class="space-y-1">
      <h1 class="text-3xl font-semibold leading-tight text-slate-900">
        {{ get_the_title() }}
      </h1>

      <div class="flex items-center gap-3 text-sm text-slate-500">
        <span>{{ get_field('market') }}</span>
        <span>·</span>
        <span>{{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}</span>
      </div>
    </div>

    <a
      href="/dashboard"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition text-sm whitespace-nowrap">
      ← Übersicht
    </a>

  </header>

  @if ($image = get_field('chart_image'))
  <div class="relative">
    <img
      src="{{ $image['url'] }}"
      alt=""
      class="w-full rounded-2xl shadow-lg">
  </div>
  @endif

  <div class="prose prose-lg max-w-none">
    {!! get_field('content_text') !!}
  </div>

</article>

@endsection

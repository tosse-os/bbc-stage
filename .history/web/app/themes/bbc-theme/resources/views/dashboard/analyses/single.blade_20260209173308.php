@extends('layouts.dashboard')

@section('content')

<article class="max-w-6xl mx-auto space-y-8">

  @php
  $terms = get_the_terms(get_the_ID(), 'analysis_market');
  $asset = $terms && !is_wp_error($terms) ? $terms[0] : null;

  $wkn = $asset ? get_term_meta($asset->term_id, 'wkn', true) : null;
  $isin = $asset ? get_term_meta($asset->term_id, 'isin', true) : null;
  @endphp

  {{-- Header --}}
  <header class="bg-white/80 backdrop-blur rounded-xl px-6 py-4 flex items-start justify-between gap-6 shadow-sm">

    <div class="space-y-1">
      <h1>{{ get_post_meta(get_the_ID(), 'report_asset_name', true) }}</h1>

      @if (
      get_post_meta(get_the_ID(), 'report_wkn', true) ||
      get_post_meta(get_the_ID(), 'report_isin', true)
      )
      <div class="mt-1 text-sm text-slate-500">
        @if ($wkn = get_post_meta(get_the_ID(), 'report_wkn', true))
        <span>WKN {{ $wkn }}</span>
        @endif

        @if (
        get_post_meta(get_the_ID(), 'report_wkn', true) &&
        get_post_meta(get_the_ID(), 'report_isin', true)
        )
        <span class="mx-1">·</span>
        @endif

        @if ($isin = get_post_meta(get_the_ID(), 'report_isin', true))
        <span>ISIN {{ $isin }}</span>
        @endif
      </div>
      @endif

      <div class="text-sm text-slate-500">
        {{ get_post_meta(get_the_ID(), 'report_market_name', true) }}
        · {{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}
      </div>

    </div>

    <a
      href="/dashboard"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition text-sm whitespace-nowrap">
      ← Übersicht
    </a>

  </header>

  {{-- Chart --}}
  @if ($image = get_field('chart_image'))
  <div class="relative">
    <img
      src="{{ $image['url'] }}"
      alt=""
      class="w-full rounded-2xl shadow-lg cursor-pointer"
      data-chart-zoom>
  </div>
  @endif

  {{-- Content --}}
  <div class="bg-white/85 backdrop-blur rounded-xl px-6 py-6 shadow-sm">
    <div class="prose prose-lg max-w-none text-slate-800">
      {!! get_field('content_text') !!}
    </div>
  </div>

</article>

@endsection

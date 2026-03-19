@extends('layouts.dashboard')

@section('content')

<article class="max-w-6xl mx-auto space-y-8">

  @php
  $terms = get_the_terms(get_the_ID(), 'analysis_market');
  $asset = $terms && !is_wp_error($terms) ? $terms[0] : null;

  $wkn = $asset ? get_term_meta($asset->term_id, 'wkn', true) : null;
  $isin = $asset ? get_term_meta($asset->term_id, 'isin', true) : null;

  $reportWkn = get_post_meta(get_the_ID(), 'report_wkn', true);
  $reportIsin = get_post_meta(get_the_ID(), 'report_isin', true);
  @endphp

  <header class="bg-white/80 backdrop-blur rounded-xl px-6 py-4 flex items-start justify-between gap-6 shadow-sm">

    <div class="space-y-1">
      <h1 class="text-1xl md:text-2xl font-semibold">{{ get_post_meta(get_the_ID(), 'report_asset_name', true) }}</h1>

      @if ($reportWkn || $reportIsin)
      <div class=" mt-1 text-sm text-slate-200 flex flex-wrap items-center gap-6">

        @if ($reportWkn)
        <div class="relative flex items-center gap-2 group">
          <span>WKN {{ $reportWkn }}</span>

          <button
            type="button"
            data-copy="{{ $reportWkn }}"
            class="copy-btn relative text-slate-400 hover:text-slate-700 transition">

            <span class="tooltip absolute -top-8 left-1/2 -translate-x-1/2
                        bg-slate-900 text-white text-xs px-2 py-1
                        rounded opacity-0 pointer-events-none
                        transition duration-200 whitespace-nowrap">
              Kopieren
            </span>

            <svg
              data-icon="copy"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2">
              <rect x="9" y="9" width="13" height="13" rx="2" />
              <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
            </svg>

            <svg
              data-icon="check"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              class="w-4 h-4 hidden"
              fill="none"
              stroke="currentColor"
              stroke-width="2">
              <path d="M20 6L9 17l-5-5" />
            </svg>

          </button>
        </div>
        @endif


        @if ($reportIsin)
        <div class="relative flex items-center gap-2 group">
          <span>ISIN {{ $reportIsin }}</span>

          <button
            type="button"
            data-copy="{{ $reportIsin }}"
            class="copy-btn relative text-slate-400 hover:text-slate-700 transition">

            <span class="tooltip absolute -top-8 left-1/2 -translate-x-1/2
                        bg-slate-900 text-white text-xs px-2 py-1
                        rounded opacity-0 pointer-events-none
                        transition duration-200 whitespace-nowrap">
              Kopieren
            </span>

            <svg data-icon="copy"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2">
              <rect x="9" y="9" width="13" height="13" rx="2" />
              <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
            </svg>

            <svg data-icon="check"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              class="w-4 h-4 hidden"
              fill="none"
              stroke="currentColor"
              stroke-width="2">
              <path d="M20 6L9 17l-5-5" />
            </svg>

          </button>
        </div>
        @endif

    </div>
    @endif

    <div class="text-sm text-slate-500 flex items-center gap-2">
      <span>{{ get_post_meta(get_the_ID(), 'report_market_name', true) }}</span>
      <span>·</span>

      @include('dashboard.icons.calendar')

      <span>
        {{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}
      </span>
      </span>
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
      class="w-full rounded-2xl shadow-lg cursor-zoom-in"
      data-chart-zoom>
  </div>
  @endif

  <div class="bg-white/85 backdrop-blur rounded-xl px-6 py-6 shadow-sm">
    <div class="prose prose-lg max-w-none text-slate-800">
      {!! get_field('content_text') !!}
    </div>
  </div>

</article>

@endsection

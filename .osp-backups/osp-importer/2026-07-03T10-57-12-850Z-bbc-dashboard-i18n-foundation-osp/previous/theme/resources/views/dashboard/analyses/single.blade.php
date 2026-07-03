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

  <header class="dashboard-card rounded-xl px-6 py-5 flex items-start justify-between gap-6 shadow-sm">

    <div class="space-y-3">

      <h1 class="text-xl md:text-2xl font-semibold tracking-tight text-heading">
        {{ get_post_meta(get_the_ID(), 'report_asset_name', true) }}
      </h1>

      @if ($reportWkn || $reportIsin)
      <div class="text-sm text-muted flex flex-wrap items-center gap-6">

        @if ($reportWkn)
        <div class="relative flex items-center gap-2 group">
          <span class="font-medium text-strong">WKN {{ $reportWkn }}</span>

          <button
            type="button"
            data-copy="{{ $reportWkn }}"
            data-label-copy="{{ pll__('Kopieren') }}"
            data-label-success="{{ pll__('Kopiert!') }}"
            class="copy-btn relative text-meta hover:text-strong transition">

            <span class="tooltip absolute -top-8 left-1/2 -translate-x-1/2
                        text-xs px-2 py-1 rounded
                        opacity-0 pointer-events-none
                        transition duration-200 whitespace-nowrap">
              {{ pll__('Kopieren') }}
            </span>

            <svg data-icon="copy" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24" class="w-4 h-4"
              fill="none" stroke="currentColor" stroke-width="2">
              <rect x="9" y="9" width="13" height="13" rx="2" />
              <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
            </svg>

            <svg data-icon="check" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24" class="w-4 h-4 hidden"
              fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 6L9 17l-5-5" />
            </svg>

          </button>
        </div>
        @endif

        @if ($reportIsin)
        <div class="relative flex items-center gap-2 group">
          <span class="font-medium text-strong">ISIN {{ $reportIsin }}</span>

          <button
            type="button"
            data-copy="{{ $reportIsin }}"
            data-label-copy="{{ pll__('Kopieren') }}"
            data-label-success="{{ pll__('Kopiert!') }}"
            class="copy-btn relative text-meta hover:text-strong transition">

            <span class="tooltip absolute -top-8 left-1/2 -translate-x-1/2
                        text-xs px-2 py-1 rounded
                        opacity-0 pointer-events-none
                        transition duration-200 whitespace-nowrap">
              {{ pll__('Kopieren') }}
            </span>

            <svg data-icon="copy" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24" class="w-4 h-4"
              fill="none" stroke="currentColor" stroke-width="2">
              <rect x="9" y="9" width="13" height="13" rx="2" />
              <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
            </svg>

            <svg data-icon="check" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24" class="w-4 h-4 hidden"
              fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 6L9 17l-5-5" />
            </svg>

          </button>
        </div>
        @endif

      </div>
      @endif

      <div class="text-sm flex items-center gap-2">
        <span class="text-strong">
          {{ get_post_meta(get_the_ID(), 'report_market_name', true) }}
        </span>

        <span class="text-meta">·</span>

        <div class="flex items-center gap-2 text-meta">
          @include('dashboard.icons.calendar')
          <span>
            {{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}
          </span>
        </div>
      </div>

    </div>

    <a
      href="/dashboard"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
             btn-secondary transition text-sm whitespace-nowrap">
      ← {{ pll__('Übersicht') }}
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

  @php
  $commentary = get_field('commentary_file');
  @endphp

  @if($commentary && !empty($commentary['url']))
  <div class="dashboard-card rounded-xl px-6 py-5 shadow-sm">

    <div class="custom-audio-wrapper flex items-center gap-4">

      <audio src="{{ $commentary['url'] }}" class="hidden-audio" preload="metadata"></audio>

      <button class="play-btn w-10 h-10 flex items-center justify-center bg-brand-primary rounded-full text-white">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
          <path d="M8 5v14l11-7z" />
        </svg>
      </button>

      <div class="flex-1 flex flex-col">
        <div class="flex justify-between text-xs text-slate-400 mb-1">
          <span class="current-time">0:00</span>
          <span class="duration">0:00</span>
        </div>

        <input type="range"
          class="seek-bar w-full h-1 accent-brand-primary cursor-pointer"
          value="0" step="0.1">
      </div>

      <button class="mute-btn text-slate-400">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
        </svg>
      </button>

    </div>

  </div>
  @endif

  <div class="dashboard-card rounded-xl px-6 py-6 shadow-sm max-w-4xl mx-auto max-w-[600px]">
    <div class="prose prose-lg max-w-none">
      {!! get_field('content_text') !!}
    </div>
  </div>
  <!-- <div class="dashboard-card rounded-xl px-6 py-6 shadow-sm">
    <div class="prose prose-lg max-w-none lg:columns-2 lg:gap-x-12">
      {!! get_field('content_text') !!}
    </div>
  </div> -->

</article>

@endsection

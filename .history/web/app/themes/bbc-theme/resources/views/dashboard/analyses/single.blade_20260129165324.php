@extends('layouts.dashboard')

@section('content')

<article class="max-w-5xl mx-auto space-y-8">

  <header class="space-y-2">
    <h1 class="text-3xl font-semibold">
      {{ get_the_title() }}
    </h1>

    <div class="text-sm text-dashboard-muted flex gap-4">
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
      class="w-full rounded-xl cursor-zoom-in"
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

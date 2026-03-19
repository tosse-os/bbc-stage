{{-- resources/views/partials/landing/market-insights.blade.php --}}

@php
$platformIcons = [
'youtube' => Vite::asset('resources/images/platforms/youtube.svg'),
//'x' => Vite::asset('resources/images/platforms/x.svg'),
//'shorts' => Vite::asset('resources/images/platforms/shorts.svg'),
];


$headline = get_field('market_insights_headline') ?: 'Market Insights – live from our analysts';
$subline = get_field('market_insights_subline') ?: 'Meet our team of experienced professionals ready to support your financial journey';
@endphp

<section id="market-insights" class="relative overflow-hidden py-28 bg-market-insights">
  <div class="absolute inset-0 pointer-events-none">
    <div class="market-pattern"></div>
  </div>

  <div class="relative container-content text-center">
    <h2 class="text-4xl font-semibold tracking-tight text-slate-900">
      {!! $headline !!}
    </h2>

    <p class="mt-4 text-lg text-slate-600">
      {!! $subline !!}
    </p>

    <div class="mt-16 grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-3">
      @for ($i = 1; $i <= 3; $i++)
        @php
        $image=get_field("market_insight_{$i}_image");
        $title=get_field("market_insight_{$i}_title");
        $link=get_field("market_insight_{$i}_link");
        $duration=get_field("market_insight_{$i}_duration");
        $platform=get_field("market_insight_{$i}_platform");
        @endphp

        @if ($image && $link)
        <a href="{{ $link }}" target="_blank"
        class="group relative block overflow-hidden rounded-2xl bg-white shadow-[0_20px_50px_rgba(0,0,0,0.12)] transition-transform duration-300 hover:-translate-y-2">

        <div class="relative aspect-video overflow-hidden">
          <img
            src="{{ $image['url'] }}"
            alt="{{ $image['alt'] ?? '' }}"
            class="h-full w-full object-cover">

          @if (!empty($platformIcons[$platform]))
          <div class="absolute left-4 top-4 h- w-9 rounded-md ">
            <img
              src="{{ $platformIcons[$platform] }}"
              alt=""
              aria-hidden="true"
              width="35"
              height="auto">
          </div>
          @endif

          @if ($duration)
          <div class="absolute bottom-3 right-3 rounded-md bg-black/70 px-2 py-1 text-xs font-medium text-white">
            {{ $duration }}
          </div>
          @endif
        </div>

        <div class="px-6 py-5 text-left">
          <h3 class="text-base font-medium text-slate-900">
            {{ $title }}
          </h3>
        </div>
        </a>
        @endif
        @endfor
    </div>

    <div class="mt-16 text-sm text-brand-primary">
      Independent market analysis. Updated daily.
    </div>
  </div>
</section>

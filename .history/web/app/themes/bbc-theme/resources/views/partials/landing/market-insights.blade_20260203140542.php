{{-- resources/views/partials/landing/market-insights.blade.php --}}

@php
$platformIcons = [
'youtube' => Vite::asset('resources/images/platforms/youtube.svg'),
'x' => Vite::asset('resources/images/platforms/x.svg'),
'shorts' => Vite::asset('resources/images/platforms/shorts.svg'),
];

$headline = get_field('market_insights_headline') ?: 'Market Insights – live from our analysts';
$subline = get_field('market_insights_subline') ?: 'Meet our team of experienced professionals ready to support your financial journey';

$normalizedHeadline = str_replace(['–', '—'], '-', $headline);
$headlineParts = explode(' - ', $normalizedHeadline, 2);
@endphp

<section
  id="market-insights"
  class="relative overflow-hidden py-28">

  {{-- Background --}}
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 market-insights-bg"></div>
    <div class="absolute inset-0 market-pattern"></div>
  </div>


  <div class="relative container-content text-center">

    {{-- Headline --}}
    <h2 class="text-4xl font-semibold tracking-tight text-slate-900">
      @if(count($headlineParts) === 2)
      {{ $headlineParts[0] }}
      <span class="text-brand-primary">– {{ $headlineParts[1] }}</span>
      @else
      {{ $headline }}
      @endif
    </h2>

    {{-- Subline --}}
    <p class="mt-4 text-lg text-slate-600">
      {{ $subline }}
    </p>

    {{-- Cards --}}
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
        <a
        href="{{ $link }}"
        target="_blank"
        class="group block overflow-hidden rounded-2xl bg-white shadow-[0_16px_40px_rgba(0,0,0,0.12)] transition-transform duration-300 hover:-translate-y-2 {{ $i === 2 ? 'market-insight--premium' : '' }}">

        <div class="relative aspect-video overflow-hidden">
          <img
            src="{{ $image['url'] }}"
            alt="{{ $image['alt'] ?? '' }}"
            class="h-full w-full object-cover">

          @if ($i === 2)
          <span class="premium-banner">
            <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <path d="M5 19H19V17H5V19ZM19 15L21 7L16 10L12 4L8 10L3 7L5 15H19Z"
                fill="currentColor" />
            </svg>
            Premium Content
          </span>
          @endif

          @if (!empty($platformIcons[$platform]))
          <div class="absolute left-4 top-4">
            <img
              src="{{ $platformIcons[$platform] }}"
              alt=""
              aria-hidden="true"
              class="min-w-[35px] max-h-[35px] w-auto">
          </div>
          @endif

          @if ($duration)
          <div class="absolute bottom-3 right-3 rounded-md bg-black/70 px-2 py-1 text-xs font-medium text-white">
            {{ $duration }}
          </div>
          @endif
        </div>

        <div class="px-6 pt-4 pb-5 text-left">
          <h3 class="text-[17px] leading-tight font-semibold tracking-tight text-brand-primary">
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

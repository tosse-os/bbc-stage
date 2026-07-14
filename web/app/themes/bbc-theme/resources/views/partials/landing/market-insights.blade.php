{{-- resources/views/partials/landing/market-insights.blade.php --}}

@php
$platformIcons = [
'youtube' => Vite::asset('resources/images/landingpage/platforms/youtube.svg'),
'x' => Vite::asset('resources/images/landingpage/platforms/x.svg'),
'shorts' => Vite::asset('resources/images/landingpage/platforms/shorts.svg'),
];

$headline = $marketInsights['headline'];
$subline = $marketInsights['subline'];
$additional = $marketInsights['additional'];
@endphp

<section
  id="market-insights"
  class="relative overflow-hidden py-28 {{-- data-scroll-offset="80" --}}">

  {{-- Background --}}
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 market-insights-bg"></div>
    <div class="absolute inset-0 market-pattern pattern-section"></div>
  </div>

  <div class="relative container-content text-center">

    {{-- Headline --}}
    <h2 class="text-4xl font-semibold tracking-tight text-slate-900">
      {!! $headline !!}
    </h2>

    {{-- Subline --}}
    <p class="mt-4 text-lg text-slate-600">
      {!! $subline !!}
    </p>

    {{-- Cards --}}
    <div class="mt-16 grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-3">
      @foreach ($marketInsights['items'] as $item)
      @php
      $image = $item['image'];
      $title = $item['title'];
      $link = $item['link'];
      $duration = $item['duration'];
      $platform = $item['platform'];
      $isPremium = $item['premium'];
      @endphp

      @if ($image && $link)
      <div class="reveal-media">
        <a
          href="{{ $link }}"
          target="_blank"
          class="group block overflow-hidden rounded-2xl bg-white shadow-[0_16px_40px_rgba(0,0,0,0.12)] transition-transform duration-300 hover:-translate-y-2 {{ $isPremium ? 'market-insight--premium' : '' }}">

          <div class="relative aspect-video overflow-hidden">
            <img
              src="{{ $image['url'] }}"
              alt="{{ $image['alt'] ?? '' }}"
              class="h-full w-full object-cover">

            @if ($isPremium)
            <span class="premium-banner">
              <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <defs>
                  <linearGradient id="goldGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#fff1b8" />
                    <stop offset="35%" stop-color="#e6c36a" />
                    <stop offset="65%" stop-color="#cfa23f" />
                    <stop offset="100%" stop-color="#9a741c" />
                  </linearGradient>
                </defs>
                <path d="M5 19H19V17H5V19ZM19 15L21 7L16 10L12 4L8 10L3 7L5 15H19Z"
                  fill="url(#goldGradient)" />
              </svg>
              {{ function_exists('pll__') ? pll__('Premium Content') : __('Premium Content', 'sage') }}
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
      </div>
      @endif
      @endforeach
    </div>

    <div class="mt-16 text-sm text-brand-primary">
      {!! $additional !!}
    </div>

    {{-- CTA --}}
    <div class="mt-14 flex flex-col items-center gap-4">

      <a href="{{ esc_url($subscribeTrialUrl) }}"
        class="inline-flex items-center justify-center rounded-full bg-gradient-to-b from-[#5aaec4] to-[#3f879c] px-10 py-4 text-base font-semibold text-white shadow-[0_18px_50px_rgba(63,135,156,0.55)] transition-all duration-300 hover:from-[#3f879c] hover:to-[#35788b] hover:shadow-[0_22px_60px_rgba(63,135,156,0.6)]">
        {{ function_exists('pll__') ? pll__('Kostenlose Analysen sichern') : __('Kostenlose Analysen sichern', 'sage') }}
      </a>

    </div>

  </div>
</section>

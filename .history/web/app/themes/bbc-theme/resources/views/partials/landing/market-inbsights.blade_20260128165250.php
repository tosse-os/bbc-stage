<section id="market-insights" class="relative overflow-hidden bg-market-insights py-28">
  <div class="absolute inset-0 pointer-events-none">
    <div class="market-pattern"></div>
  </div>

  <div class="relative container-content text-center">
    <h2 class="text-4xl font-semibold tracking-tight text-slate-900">
      Market Insights <span class="text-brand-primary">– live from our analysts</span>
    </h2>

    <p class="mt-4 text-lg text-slate-600">
      Meet our team of experienced professionals ready to support your financial journey
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

          @if ($platform)
          <div class="absolute left-3 top-3 h-9 w-9 rounded-md bg-white/90 p-1.5">
            <img
              src="{{ Vite::asset("resources/images/platforms/{$platform}.svg") }}"
              alt="{{ $platform }}">
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

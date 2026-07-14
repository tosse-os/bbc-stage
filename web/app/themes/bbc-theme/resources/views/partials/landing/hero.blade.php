<section class="relative overflow-hidden bg-slate-950">
  <div class="absolute inset-0">
    <img
      src="{{ Vite::asset('resources/images/landingpage/hero-bg.jpg') }}"
      alt=""
      class="h-full w-full object-cover opacity-80">
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/40 via-slate-950/20 to-transparent"></div>
  </div>

  <div class="relative">
    <div class="container-content pt-28 pb-28 sm:pt-32 lg:pt-40 lg:pb-32">
      <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">

        <div>
          <h1 class="reveal-text text-4xl font-semibold tracking-tight text-white lg:text-5xl" data-reveal-delay="0">
            {!! $hero['headline'] !!}

          </h1>

          <p class="reveal-text mt-6 max-w-xl text-lg text-slate-300" data-reveal-delay="150">
            {!! $hero['subline'] !!}
          </p>

          <div class="reveal-text mt-10">
            <a
              href="{{ esc_url($subscribeTrialUrl) }}"
              target="_self"
              class="inline-flex items-center rounded-xl bg-brand-primary px-6 py-3 text-base font-medium text-white transition hover:bg-brand-primaryHover" data-reveal-delay="300">
              {!! $hero['cta_text'] !!}
            </a>
          </div>
        </div>

        @if($hero['visual'])
        <div class="relative reveal-media element-float" data-reveal-delay="450">
          <img
            src="{{ $hero['visual']['url'] }}"
            alt="{{ $hero['visual']['alt'] }}">
        </div>
        @endif

      </div>

      <div class="reveal-text mt-20 text-center text-sm text-slate-400" data-reveal-delay="600">
        {!! $hero['additional_text'] !!}
      </div>
    </div>
  </div>
</section>

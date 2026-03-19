<section class="relative overflow-hidden bg-brand-dark">
  <div class="absolute inset-0">
    <img
      src="{{ Vite::asset('resources/images/hero-bg.jpg') }}"
      alt=""
      class="h-full w-full object-cover opacity-60">
    <div class="absolute inset-0 bg-gradient-to-r from-brand-dark/95 via-brand-dark/70 to-transparent"></div>
  </div>

  <div class="relative mx-auto max-w-7xl px-6 pt-44 pb-32">
    <div class="grid grid-cols-1 items-center gap-20 lg:grid-cols-2">
      <div>
        <h1 class="text-4xl font-semibold tracking-tight text-white lg:text-5xl">
          {!! str_replace(
          ['Bridge', 'Success'],
          ['<span class="text-brand-primary">Bridge</span>', '<span class="text-brand-primary">Success</span>'],
          e($hero['headline'])
          ) !!}
        </h1>

        <p class="mt-6 max-w-xl text-lg text-slate-300">
          {{ $hero['subline'] }}
        </p>

        <div class="mt-10">
          <a
            href="{{ $hero['cta_link']['url'] }}"
            target="{{ $hero['cta_link']['target'] ?: '_self' }}"
            class="inline-flex items-center rounded-xl bg-brand-primary px-7 py-3 text-base font-medium text-white hover:bg-brand-primaryHover transition">
            {{ $hero['cta_text'] }}
          </a>
        </div>
      </div>

      @if($hero['visual'])
      <div class="relative flex justify-center lg:justify-end">
        <img
          src="{{ $hero['visual']['url'] }}"
          alt="{{ $hero['visual']['alt'] }}"
          class="w-[320px] lg:w-[360px] drop-shadow-2xl">
      </div>
      @endif
    </div>

    <div class="m

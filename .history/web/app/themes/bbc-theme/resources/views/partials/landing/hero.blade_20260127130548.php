<section class="relative overflow-hidden bg-slate-950">
  <div class="absolute inset-0">
    <img
      src="{{ Vite::asset('resources/images/hero-bg.jpg') }}"
      alt=""
      class="h-full w-full object-cover opacity-60">
    <div class="absolute inset-0 "></div>
  </div>

  <div class="relative mx-auto max-w-7xl px-6 pt-40 pb-32">
    <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
      <div>
        <h1 class="text-4xl font-semibold tracking-tight text-white lg:text-5xl">
          {{ $hero['headline'] }}
        </h1>

        <p class="mt-6 max-w-xl text-lg text-slate-300">
          {{ $hero['subline'] }}
        </p>

        <div class="mt-10">
          <a
            href="{{ $hero['cta_link']['url'] }}"
            target="{{ $hero['cta_link']['target'] ?: '_self' }}"
            class="inline-flex items-center rounded-xl bg-cyan-600 px-6 py-3 text-base font-medium text-white hover:bg-cyan-500 transition">
            {{ $hero['cta_text'] }}
          </a>
        </div>
      </div>

      @if($hero['visual'])
      <div class="relative">
        <img
          src="{{ $hero['visual']['url'] }}"
          alt="{{ $hero['visual']['alt'] }}"
          class="">
      </div>
      @endif
    </div>

    <div class="mt-20 text-center text-sm text-slate-400">
      45 Börsenplätze · permanente Updates · tägliche Analysen
    </div>
  </div>
</section>

<section class="relative overflow-hidden bg-slate-900">
  <div class="mx-auto max-w-7xl px-6 py-24">
    <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">
      <div>
        <h1 class="text-4xl font-semibold tracking-tight text-white lg:text-5xl">
          {{ $hero['headline'] }}
        </h1>

        <p class="mt-6 max-w-xl text-lg text-slate-300">
          {{ $hero['subline'] }}
        </p>

        @if($hero['cta_link'])
        <div class="mt-10">
          <a
            href="{{ $hero['cta_link']['url'] }}"
            target="{{ $hero['cta_link']['target'] ?: '_self' }}"
            class="inline-flex items-center rounded-xl bg-blue-500 px-6 py-3 text-base font-medium text-white transition hover:bg-blue-400">
            {{ $hero['cta_text'] }}
          </a>
        </div>
        @endif
      </div>

      @if($hero['visual'])
      <div class="relative">
        <img
          src="{{ $hero['visual']['url'] }}"
          alt="{{ $hero['visual']['alt'] }}"
          class="rounded-2xl shadow-2xl">
      </div>
      @endif
    </div>
  </div>
</section>

<section id="about" class="bg-white py-24 scroll-mt-24">
  <div class="mx-auto max-w-7xl px-6">
    <div class="max-w-3xl">
      @if(!empty($about['headline']))
      <h2 class="text-4xl font-semibold tracking-tight text-slate-900">
        {!! $about['headline'] !!}
      </h2>
      @endif

      @if(!empty($about['intro']))
      <p class="mt-6 text-base text-slate-600">
        {!! $about['intro'] !!}
      </p>
      @endif
    </div>

    <div class="mt-16 grid grid-cols-1 gap-x-16 gap-y-12 md:grid-cols-2">
      @for($i = 1; $i <= 4; $i++)
        @php
        $label=$about["feature_{$i}_label"] ?? null;
        $headline=$about["feature_{$i}_headline"] ?? null;
        $text=$about["feature_{$i}_text"] ?? null;
        @endphp

        @if($label || $headline || $text)
        <div class="flex gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-medium text-brand-primary">
          {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
        </div>

        <div>
          @if($label)
          <div class="text-sm font-semibold text-slate-900">
            {!! $label !!}
          </div>
          @endif

          @if($headline)
          <div class="mt-1 text-sm text-slate-700">
            {!! $headline !!}
          </div>
          @endif

          @if($text)
          <div class="mt-2 text-sm leading-relaxed text-slate-500">
            {!! nl2br(e($text)) !!}
          </div>
          @endif
        </div>
    </div>
    @endif
    @endfor
  </div>

  @if(!empty($about['link']['url']))
  <div class="mt-12">
    <a
      href="{{ $about['link']['url'] }}"
      target="{{ $about['link']['target'] ?? '_self' }}"
      class="inline-flex items-center gap-2 text-sm font-medium text-brand-primary hover:text-brand-primaryHover transition">
      {{ $about['link']['title'] }}
      <span aria-hidden="true">→</span>
    </a>
  </div>
  @endif
  </div>
</section>

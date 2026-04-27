<section id="about" class="bg-[#F8F7F7] py-24" {{-- data-scroll-offset="80" --}}>
  <div class="container-content">

    <div class="grid grid-cols-1 items-start gap-16 lg:grid-cols-[2fr_3fr]">

      <div>
        @if($about['visual'])
        <img
          src="{{ $about['visual']['url'] }}"
          alt="{{ $about['visual']['alt'] ?? '' }}"
          class="reveal-media w-full max-w-md">
        @endif
      </div>

      <div>
        @if($about['headline'])
        <h2 class="reveal-text text-4xl font-semibold tracking-tight text-slate-900" data-reveal-delay="0">
          {!! $about['headline'] !!}
        </h2>
        @endif

        @if($about['intro'])
        <p class="reveal-text mt-6 text-lg text-slate-600" data-reveal-delay="150">
          {!! $about['intro'] !!}
        </p>
        @endif

        <div class="mt-12 grid grid-cols-1 gap-x-10 gap-y-12 md:grid-cols-2" data-reveal-delay="300">
          @foreach($about['features'] as $i => $feature)

          @if($feature)
          @php $itemDelay = 450 + ($i * 150); @endphp

          <div class="reveal-text flex gap-4" data-reveal-delay="{{ $itemDelay }}">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-lg font-medium text-brand-primary shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
              {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
            </div>

            <div>
              @if(!empty($feature['label']))
              <div class="text-lg font-semibold text-slate-900">
                {!! $feature['label'] !!}
              </div>
              @endif

              @if(!empty($feature['headline']))
              <div class="mt-1 text-base font-semibold text-slate-700">
                {!! $feature['headline'] !!}
              </div>
              @endif

              @if(!empty($feature['text']))
              <div class="mt-2 text-base leading-relaxed text-slate-500">
                {!! nl2br(e($feature['text'])) !!}
              </div>
              @endif
            </div>
          </div>

          @endif

          @endforeach
        </div>

      </div>
    </div>

  </div>
</section>

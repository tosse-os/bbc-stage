<section id="about" class="bg-[#F8F7F7] py-24 scroll-mt-14">
  <div class="container-content">
    @php
    $headline = get_field('about_section_headline');
    $intro = get_field('about_intro');
    $visual = get_field('about_visual');
    @endphp

    <div class="grid grid-cols-1 items-start gap-16 lg:grid-cols-[2fr_3fr]">

      <div>
        @if($visual)
        <img
          src="{{ $visual['url'] }}"
          alt="{{ $visual['alt'] ?? '' }}"
          class="reveal-media w-full max-w-md">
        @endif
      </div>

      <div>
        @if($headline)
        <h2 class="reveal-text text-4xl font-semibold tracking-tight text-slate-900">
          {!! $headline !!}
        </h2>
        @endif

        @if($intro)
        <p class="reveal-text mt-6 text-lg text-slate-600">
          {!! $intro !!}
        </p>
        @endif

        <div class="mt-12 grid grid-cols-1 gap-x-10 gap-y-12 md:grid-cols-2">
          @for($i = 1; $i <= 4; $i++)
            @php
            $label=get_field("about_feature_{$i}_label");
            $headlineFeature=get_field("about_feature_{$i}_headline");
            $text=get_field("about_feature_{$i}_text");
            @endphp

            @if($label || $headlineFeature || $text)
            <div class="reveal-text flex gap-4">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-slate-200 text-lg font-medium text-brand-primary shadow-[0_1px_3px_rgba(0,0,0,0.06)]">
              {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
            </div>

            <div>
              @if($label)
              <div class="text-base font-semibold text-slate-900">
                {!! $label !!}
              </div>
              @endif

              @if($headlineFeature)
              <div class="mt-1 text-base font-semibold text-slate-700">
                {!! $headlineFeature !!}
              </div>
              @endif

              @if($text)
              <div class="mt-2 text-base leading-relaxed text-slate-500">
                {!! nl2br(e($text)) !!}
              </div>
              @endif
            </div>
        </div>
        @endif
        @endfor
      </div>
    </div>

  </div>
  </div>
</section>

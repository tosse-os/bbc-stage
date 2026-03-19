<section id="about" class="bg-white py-24 scroll-mt-24">
  <div class="mx-auto max-w-7xl px-6">
    @php
    $headline = get_field('about_section_headline');
    $intro = get_field('about_intro');
    $visual = get_field('about_visual');
    @endphp

    <div class="grid grid-cols-1 items-start gap-16 lg:grid-cols-2">
      <div>
        @if($visual)
        <img
          src="{{ $visual['url'] }}"
          alt="{{ $visual['alt'] ?? '' }}"
          class="w-full max-w-md">
        @endif
      </div>

      <div>
        @if($headline)
        <h2 class="text-4xl font-semibold tracking-tight text-slate-900">
          {!! $headline !!}
        </h2>
        @endif

        @if($intro)
        <p class="mt-6 text-base text-slate-600">
          {!! $intro !!}
        </p>
        @endif

        <div class="mt-12 grid grid-cols-1 gap-x-16 gap-y-12 md:grid-cols-2">
          @for($i = 1; $i <= 4; $i++)
            @php
            $label=get_field("about_feature_{$i}_label");
            $headlineFeature=get_field("about_feature_{$i}_headline");
            $text=get_field("about_feature_{$i}_text");
            @endphp

            @if($label || $headlineFeature || $text)
            <div class="flex gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-base font-medium text-brand-primary">
              {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
            </div>

            <div>
              @if($label)
              <div class="text-base font-semibold text-slate-900">
                {!! $label !!}
              </div>
              @endif

              @if($headlineFeature)
              <div class="mt-1 text-sm font-semibold text-slate-700">
                {!! $headlineFeature !!}
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
    </div>
  </div>
  </div>
</section>

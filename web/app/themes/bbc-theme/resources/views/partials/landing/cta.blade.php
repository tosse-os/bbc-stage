@php
$ctaData = is_array($cta ?? null) ? $cta : [];

$getCtaValue = static function (array $keys, $fallback = null) use ($ctaData) {
    foreach ($keys as $key) {
        if (array_key_exists($key, $ctaData) && $ctaData[$key] !== null && $ctaData[$key] !== '') {
            return $ctaData[$key];
        }
    }

    foreach ($keys as $key) {
        if (function_exists('get_field')) {
            $value = get_field('cta_' . $key);

            if ($value !== null && $value !== false && $value !== '') {
                return $value;
            }
        }
    }

    return $fallback;
};

$headlineTop = $getCtaValue(['headline_top'], 'Handeln Sie nicht schneller.');
$headlineMain = $getCtaValue(['headline_main', 'headline_main_text'], null);
$oldHeadlineHighlight = $getCtaValue(['headline_highlight'], null);

if (! $headlineMain) {
    $headlineMainText = 'Handeln Sie';
    $headlineMainHighlight = $oldHeadlineHighlight ?: 'strukturierter.';
    $headlineMain = '<span class="drop-shadow-[0_2px_4px_rgba(0,0,0,0.18)]">' . esc_html($headlineMainText) . '</span> <span class="text-brand-primary">' . esc_html($headlineMainHighlight) . '</span>';
}

$subline = $getCtaValue(['subline'], "Märkte werden sich weiter bewegen.\nDie Frage ist nicht ob, sondern <strong>wie vorbereitet</strong> Sie sind.");
$buttonText = $getCtaValue(['button_text'], 'Kostenlose Analysen sichern');
$buttonLink = $getCtaValue(['button_link'], '/subscribe-trial');
$note = $getCtaValue(['note'], 'Kein Risiko · 7 Tage kostenlos · jederzeit kündbar');

$buttonUrl = is_array($buttonLink) ? ($buttonLink['url'] ?? '/subscribe-trial') : (string) $buttonLink;
$buttonTarget = is_array($buttonLink) ? ($buttonLink['target'] ?: '_self') : '_self';

$allowedHeadlineHtml = [
    'br' => [],
    'span' => [
        'class' => true,
    ],
    'strong' => [],
    'em' => [],
];

$allowedSublineHtml = [
    'br' => [],
    'strong' => [],
    'em' => [],
    'span' => [
        'class' => true,
    ],
];
@endphp

<section id="ct" class="relative overflow-hidden py-24 lg:py-32 scroll-mt-15">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-cta"></div>
    <div class="absolute inset-0 cta-pattern pattern-section"></div>
  </div>

  <div class="container-content text-center">

    @if($headlineTop)
    <h2 class="text-3xl lg:text-4xl font-light text-slate-500 mb-3">
      {{ $headlineTop }}
    </h2>
    @endif

    @if($headlineMain)
    <h3 class="mt-1 text-4xl lg:text-5xl font-semibold text-white leading-tight tracking-tight">
      {!! wp_kses($headlineMain, $allowedHeadlineHtml) !!}
    </h3>
    @endif

    @if($subline)
    <p class="mt-6 max-w-lg mx-auto text-base lg:text-lg text-slate-500/80 leading-relaxed">
      {!! wp_kses(nl2br($subline, false), $allowedSublineHtml) !!}
    </p>
    @endif

    @if($buttonText && $buttonUrl)
    <div class="mt-14 flex flex-col items-center gap-4">
      <a
        href="{{ esc_url($buttonUrl) }}"
        target="{{ esc_attr($buttonTarget) }}"
        @if($buttonTarget === '_blank') rel="noopener noreferrer" @endif
        class="inline-flex items-center justify-center rounded-full bg-gradient-to-b from-[#5aaec4] to-[#3f879c] px-10 py-4 text-base font-semibold text-white shadow-[0_18px_50px_rgba(63,135,156,0.55)] transition-all duration-300 hover:from-[#3f879c] hover:to-[#35788b] hover:shadow-[0_22px_60px_rgba(63,135,156,0.6)]">
        {{ $buttonText }}
      </a>

      @if($note)
      <div class="mt-4 text-sm text-slate-500/70">
        {{ $note }}
      </div>
      @endif
    </div>
    @endif

  </div>
</section>

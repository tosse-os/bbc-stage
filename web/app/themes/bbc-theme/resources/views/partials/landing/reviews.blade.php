@php
$reviewsData = $reviews ?? [];
$reviewItems = $reviewsData['items'] ?? [];
$settings = $reviewsData['settings'] ?? [];
$strings = $reviewsData['strings'] ?? [];
$autoplay = ! empty($settings['autoplay']) ? '1' : '0';
$speed = (int) ($settings['speed'] ?? 5000);
$perStep = (int) ($settings['per_step'] ?? 1);
$equalHeight = ! empty($settings['equal_height']) ? '1' : '0';
$equalTextLength = ! empty($settings['equal_text_length']);
$textLimit = max(0, (int) ($settings['text_limit'] ?? 260));
$readMore = $strings['read_more'] ?? (function_exists('pll__') ? pll__('Read more') : __('Read more', 'sage'));
$showLess = $strings['show_less'] ?? (function_exists('pll__') ? pll__('Show less') : __('Show less', 'sage'));
@endphp

@if (! empty($reviewItems))
<section id="reviews" class="relative overflow-hidden py-20 lg:py-28 scroll-mt-15" aria-labelledby="reviews-title">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-reviews"></div>
    <div class="absolute inset-0 reviews-pattern pattern-section"></div>
  </div>

  <div class="relative container-content">

    <div class="mb-6 lg:mb-8 text-center">
      @if(!empty($strings['eyebrow']))
      <p class="reveal-text mx-auto max-w-xl text-sm lg:text-base text-slate-600">
        {!! $strings['eyebrow'] !!}
      </p>
      @endif

      @if(!empty($strings['headline']))
      <h2 id="reviews-title" class="reveal-text mt-4 text-3xl lg:text-4xl font-semibold tracking-tight text-brand-primaryFontDark">
        {!! $strings['headline'] !!}
      </h2>
      @endif
    </div>

    <div
      class="reviews-slider"
      data-reviews-slider
      data-autoplay="{{ $autoplay }}"
      data-speed="{{ $speed }}"
      data-per-step="{{ $perStep }}"
      data-equal-height="{{ $equalHeight }}">
      <div class="reviews-slider__viewport">
        <div class="reviews-slider__track">
          @foreach ($reviewItems as $review)
          @php
          $rating = max(0, min(5, (int) ($review['rating'] ?? 5)));
          $name = $review['name'] ?? '';
          $position = $review['position'] ?? '';
          $company = $review['company'] ?? '';
          $image = $review['image'] ?? [];
          $initial = $name ? mb_substr($name, 0, 1) : 'R';
          $text = trim((string) ($review['text'] ?? ''));
          $plainText = trim(wp_strip_all_tags($text));
          $textLength = mb_strlen($plainText);
          $needsToggle = $equalTextLength && $textLimit > 0 && $textLength > $textLimit;
          $shortText = $needsToggle ? rtrim(mb_substr($plainText, 0, $textLimit)) . '…' : $plainText;
          @endphp

          <div class="reviews-slider__slide">
            <article class="review-card">
              <div class="review-card__top">
                @if (! empty($image['url']))
                <img
                  class="review-card__image"
                  src="{{ esc_url($image['url']) }}"
                  alt="{{ esc_attr($image['alt'] ?? $name) }}"
                  loading="lazy">
                @else
                <span class="review-card__avatar" aria-hidden="true">{{ $initial }}</span>
                @endif

                <div class="review-card__person">
                  @if ($name)
                  <strong class="review-card__name">{{ $name }}</strong>
                  @endif

                  @if ($position || $company)
                  <span class="review-card__meta">
                    {{ $position }}@if ($position && $company), @endif{{ $company }}
                  </span>
                  @endif
                </div>
              </div>

              <div class="review-stars" aria-label="{{ $rating }}/5">
                @for ($i = 1; $i <= 5; $i++)
                  <span aria-hidden="true" class="{{ $i <= $rating ? 'is-filled' : 'is-empty' }}">★</span>
                  @endfor
              </div>

              @if ($plainText !== '')
              <blockquote class="review-card__quote {{ $needsToggle ? 'is-collapsible' : '' }}">
                <span class="review-card__quote-short">{!! nl2br(e($shortText)) !!}</span>

                @if ($needsToggle)
                <span class="review-card__quote-full">{!! nl2br(e($plainText)) !!}</span>

                <button class="review-card__toggle" type="button" data-review-toggle aria-expanded="false">
                  <span class="review-card__toggle-more">{{ $readMore }}</span>
                  <span class="review-card__toggle-less">{{ $showLess }}</span>
                </button>
                @endif
              </blockquote>
              @endif
            </article>
          </div>
          @endforeach
        </div>
      </div>

      <div class="reviews-slider__nav" aria-label="{{ esc_attr($strings['eyebrow'] ?? '') }}">
        <button class="reviews-slider__button" type="button" data-reviews-prev aria-label="{{ esc_attr($strings['previous'] ?? 'Previous review') }}">
          <span aria-hidden="true">‹</span>
        </button>

        <div class="reviews-slider__dots" data-reviews-dots></div>

        <button class="reviews-slider__button" type="button" data-reviews-next aria-label="{{ esc_attr($strings['next'] ?? 'Next review') }}">
          <span aria-hidden="true">›</span>
        </button>
      </div>
    </div>

  </div>
</section>
@endif

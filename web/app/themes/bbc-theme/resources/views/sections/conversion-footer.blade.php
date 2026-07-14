@php
$currentLanguage = function_exists('pll_current_language') ? pll_current_language('slug') : 'de';

$translatedFooterPageUrl = static function (array $slugs, string $fallback) use ($currentLanguage): string {
    foreach ($slugs as $slug) {
        $page = get_page_by_path($slug);

        if (! $page) {
            continue;
        }

        $pageId = (int) $page->ID;

        if (function_exists('pll_get_post')) {
            $translatedId = pll_get_post($pageId, $currentLanguage);

            if ($translatedId) {
                $pageId = (int) $translatedId;
            }
        }

        $permalink = get_permalink($pageId);

        if ($permalink) {
            return $permalink;
        }
    }

    return home_url($fallback);
};

$imprintUrl = $translatedFooterPageUrl(['imprint', 'impressum'], '/imprint/');
$privacyUrl = $translatedFooterPageUrl(['datenschutz', 'privacy-policy', 'privacy'], '/datenschutz/');
@endphp

<footer class="py-8">
  <div class="container-content flex flex-col items-center gap-4 text-sm text-slate-400 lg:flex-row lg:justify-between">

    <div>
      © {{ date('Y') }} Bloombridge Capital
    </div>

    <div class="flex gap-6">
      <a href="{{ esc_url($imprintUrl) }}" class="transition hover:text-brand-primary">{!! pll__('Impressum') !!}</a>
      <a href="{{ esc_url($privacyUrl) }}" class="transition hover:text-brand-primary">{!! pll__('Datenschutz') !!}</a>
    </div>

  </div>
</footer>

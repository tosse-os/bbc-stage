<section class="max-w-7xl">

  {{-- Header --}}
  <header class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold">
      Analyses Overview
    </h1>

    {{-- View Switcher --}}
    @php
    $view = request()->get('view', 'default');
    if (!in_array($view, ['default', 'grid', 'list'], true)) {
    $view = 'default';
    }
    @endphp

    <div class="inline-flex items-center gap-1
            rounded-xl border border-brand-primary/30
            bg-white/70 backdrop-blur
            p-1 shadow-sm">

      <a href="{{ request()->fullUrlWithQuery(['view' => 'default']) }}"
        class="p-2 rounded-lg transition
            {{ $view === 'default'
              ? 'bg-brand-primary text-white'
              : 'text-brand-primary hover:bg-brand-primary/10' }}">
        @include('dashboard.icons.view-table')
      </a>

      <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
        class="p-2 rounded-lg transition
            {{ $view === 'grid'
              ? 'bg-brand-primary text-white'
              : 'text-brand-primary hover:bg-brand-primary/10' }}">
        @include('dashboard.icons.view-grid')
      </a>

      <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
        class="p-2 rounded-lg transition
            {{ $view === 'list'
              ? 'bg-brand-primary text-white'
              : 'text-brand-primary hover:bg-brand-primary/10' }}">
        @include('dashboard.icons.view-list')
      </a>

    </div>

  </header>

  @php
  $currentAsset = request()->get('asset');
  $currentMarket = request()->get('market');

  $args = [
  'post_type' => 'analysis',
  'posts_per_page' => 20,
  'orderby' => 'meta_value',
  'meta_key' => 'publish_date',
  'order' => 'DESC',
  ];

  if ($currentAsset) {
  $args['tax_query'] = [[
  'taxonomy' => 'analysis_market',
  'field' => 'slug',
  'terms' => $currentAsset,
  ]];
  } elseif ($currentMarket) {
  $args['tax_query'] = [[
  'taxonomy' => 'analysis_market',
  'field' => 'slug',
  'terms' => $currentMarket,
  ]];
  }

  $query = new WP_Query($args);
  @endphp

  @include('dashboard.analyses.filters')

  {{-- ===================== --}}
  {{-- DEFAULT VIEW (TABELLE) --}}
  {{-- ===================== --}}
  @if ($view === 'default')

  <div class="bg-white rounded-xl shadow-sm">

    <div class="grid grid-cols-[160px_140px_1fr_140px_160px] px-6 py-4 gap-4 border-b text-sm font-medium text-slate-500">
      <div>Asset</div>
      <div>Preview</div>
      <div>Description</div>
      <div>Date</div>
      <div></div>
    </div>

    @if ($query->have_posts())
    <div class="divide-y">
      @while ($query->have_posts())
      @php
      $query->the_post();
      $terms = get_the_terms(get_the_ID(), 'analysis_market');
      $primaryMarket = null;
      if ($terms) {
      foreach ($terms as $term) {
      if ($term->parent !== 0) {
      $primaryMarket = $term;
      break;
      }
      }
      }
      @endphp

      <div class="grid grid-cols-[160px_140px_1fr_140px_160px] px-6 py-5 gap-4 element-border">

        <div class="font-medium">{{ $primaryMarket?->name }}</div>

        <div class="chart-preview">
          @if ($image = get_field('chart_image'))
          <img
            src="{{ $image['sizes']['medium'] }}"
            class="rounded-md">
          @endif
        </div>

        <p class="text-sm text-slate-600">
          {{ mb_strimwidth(strip_tags(get_field('content_text')), 0, 300, '…') }}
        </p>

        <div class="text-sm text-slate-500">
          {{ date_i18n('d M, Y', strtotime(get_field('publish_date'))) }}
        </div>

        <div>
          <a href="/analysis/{{ get_post_field('post_name', get_the_ID()) }}"
            class="text-sm bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-md">
            View →
          </a>
        </div>

      </div>
      @endwhile
    </div>
    @php wp_reset_postdata(); @endphp
    @endif

  </div>
  @endif

  {{-- ================= --}}
  {{-- GRID VIEW (2-SPALTIG) --}}
  {{-- ================= --}}
  @if ($view === 'grid')

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @while ($query->have_posts())
    @php
    $query->the_post();
    $terms = get_the_terms(get_the_ID(), 'analysis_market');
    $primaryMarket = null;
    if ($terms) {
    foreach ($terms as $term) {
    if ($term->parent !== 0) {
    $primaryMarket = $term;
    break;
    }
    }
    }
    @endphp

    <div class="bg-white rounded-xl shadow-sm p-5 space-y-3">

      <div class="font-semibold">{{ $primaryMarket?->name }}</div>

      @if ($image = get_field('chart_image'))
      <img src="{{ $image['sizes']['medium'] }}" class="rounded-md">
      @endif

      <p class="text-sm text-slate-600">
        {{ mb_strimwidth(strip_tags(get_field('content_text')), 0, 220, '…') }}
      </p>

      <div class="text-sm text-slate-500">
        {{ date_i18n('d M, Y', strtotime(get_field('publish_date'))) }}
      </div>

      <a href="/analysis/{{ get_post_field('post_name', get_the_ID()) }}"
        class="text-sm text-brand-primary font-medium">
        View Report →
      </a>

    </div>
    @endwhile
    @php wp_reset_postdata(); @endphp
  </div>
  @endif

  {{-- ================= --}}
  {{-- LIST VIEW (TEXT ONLY) --}}
  {{-- ================= --}}
  @if ($view === 'list')

  <div class="bg-white rounded-xl shadow-sm divide-y">
    @while ($query->have_posts())
    @php
    $query->the_post();
    $terms = get_the_terms(get_the_ID(), 'analysis_market');
    $primaryMarket = null;
    if ($terms) {
    foreach ($terms as $term) {
    if ($term->parent !== 0) {
    $primaryMarket = $term;
    break;
    }
    }
    }
    @endphp

    <div class="px-6 py-4 flex items-center justify-between">

      <div>
        <div class="font-medium">{{ $primaryMarket?->name }}</div>
        <div class="text-sm text-slate-500">
          {{ date_i18n('d M, Y', strtotime(get_field('publish_date'))) }}
        </div>
      </div>

      <a href="/analysis/{{ get_post_field('post_name', get_the_ID()) }}"
        class="text-sm text-brand-primary">
        View →
      </a>

    </div>
    @endwhile
    @php wp_reset_postdata(); @endphp
  </div>
  @endif

</section>

<section class="max-w-7xl">

  {{-- Header --}}
  <header class="mb-6 flex items-center justify-between">
    <h1 class="text-lg md-text-2xl font-semibold">
      Analyses Overview
    </h1>

    @php
    $view = request()->get('view', 'default');
    if (!in_array($view, ['default', 'grid', 'list'], true)) {
    $view = 'default';
    }
    @endphp

    <div class="inline-flex items-center gap-1 rounded-xl border border-brand-primary/30 bg-white/70 backdrop-blur p-1 shadow-sm">

      <a href="{{ request()->fullUrlWithQuery(['view' => 'default']) }}"
        class="p-2 rounded-lg transition {{ $view === 'default' ? 'bg-brand-primary text-white' : 'text-brand-primary hover:bg-brand-primary/10' }}">
        @include('dashboard.icons.view-table')
      </a>

      <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
        class="p-2 rounded-lg transition {{ $view === 'grid' ? 'bg-brand-primary text-white' : 'text-brand-primary hover:bg-brand-primary/10' }}">
        @include('dashboard.icons.view-grid')
      </a>

      <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"
        class="p-2 rounded-lg transition {{ $view === 'list' ? 'bg-brand-primary text-white' : 'text-brand-primary hover:bg-brand-primary/10' }}">
        @include('dashboard.icons.view-list')
      </a>

    </div>
  </header>

  @php
  $currentAsset = sanitize_title((string) request()->get('asset'));
  $currentMarket = sanitize_title((string) request()->get('market'));
  $dateFromRaw = (string) request()->get('date_from');
  $dateToRaw = (string) request()->get('date_to');
  $dateFrom = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFromRaw) ? $dateFromRaw : '';
  $dateTo = preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateToRaw) ? $dateToRaw : '';

  $args = [
  'post_type' => 'analysis',
  'posts_per_page' => 20,
  'orderby' => 'meta_value',
  'meta_key' => 'publish_date',
  'order' => 'DESC',
  ];

  $metaQuery = [];

  if ($dateFrom !== '') {
  $metaQuery[] = [
  'key' => 'publish_date',
  'value' => $dateFrom,
  'compare' => '>=',
  'type' => 'DATE',
  ];
  }

  if ($dateTo !== '') {
  $metaQuery[] = [
  'key' => 'publish_date',
  'value' => $dateTo,
  'compare' => '<=',
  'type' => 'DATE',
  ];
  }

  if ($metaQuery) {
  $args['meta_query'] = $metaQuery;
  }

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

  @if ($view === 'default')

  <div class="bg-white rounded-xl shadow-sm overflow-hidden hidden md:block">

    <div class="grid grid-cols-[160px_140px_1fr_140px_160px] px-6 py-4 gap-4 border-b border-slate-100 text-sm font-medium text-slate-500 bg-slate-50/50">
      <div>Asset</div>
      <div>Preview</div>
      <div>Description</div>
      <div>Date</div>
      <div></div>
    </div>

    @if ($query->have_posts())
    <div class="flex flex-col">
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

      <a href="/analysis/{{ get_post_field('post_name', get_the_ID()) }}"
        class="grid grid-cols-[160px_140px_1fr_140px_160px] px-6 py-5 gap-4 element-border-t first:border-t-0
               hover:bg-slate-50/80 transition-all duration-200 group cursor-pointer
               hover:-translate-y-0.5 hover:shadow-sm
               focus:outline-none focus:ring-2 focus:ring-brand-primary/30">

        <div class="font-medium text-slate-900 group-hover:text-brand-primary transition-colors">
          {{ $primaryMarket?->name }}
        </div>

        <div class="chart-preview">
          @if ($image = get_field('chart_image'))
          <img
            src="{{ dashboard_secure_media_url($image['ID'] ?? $image['id'] ?? 0, 'medium') }}"
            class="rounded-md shadow-sm border border-slate-100 object-cover w-full h-16">
          @endif
        </div>

        <p class="text-sm text-slate-600 leading-relaxed">
          {{ mb_strimwidth(strip_tags(get_field('content_text')), 0, 300, '…') }}
        </p>

        <div class="text-sm text-slate-500 flex items-center">
          {{ date_i18n('d M, Y', strtotime(get_field('publish_date'))) }}
        </div>

        <div class="flex items-center justify-end">
          <span
            class="btn btn-primary btn-md">
            View Analysis →
          </span>
        </div>

      </a>
      @endwhile
    </div>
    @php wp_reset_postdata(); @endphp
    @endif

  </div>

  <div class="md:hidden space-y-4">

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

    <a href="/analysis/{{ get_post_field('post_name', get_the_ID()) }}"
      class="block bg-white rounded-xl shadow-sm p-4 space-y-3 group
             transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg
             focus:outline-none focus:ring-2 focus:ring-brand-primary/30">

      <div class="font-semibold text-slate-900 group-hover:text-brand-primary transition-colors">
        {{ $primaryMarket?->name }}
      </div>

      @if ($image = get_field('chart_image'))
      <img src="{{ dashboard_secure_media_url($image['ID'] ?? $image['id'] ?? 0, 'medium') }}" class="rounded-md">
      @endif

      <p class="text-sm text-slate-600">
        {{ mb_strimwidth(strip_tags(get_field('content_text')), 0, 180, '…') }}
      </p>

      <div class="flex items-center justify-between text-sm text-slate-500">
        <span>{{ date_i18n('d M, Y', strtotime(get_field('publish_date'))) }}</span>
        <span class="text-brand-primary font-medium transition-transform duration-200 group-hover:translate-x-1">
          View →
        </span>
      </div>

    </a>
    @endwhile

    @php wp_reset_postdata(); @endphp
  </div>

  @endif

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

    <a href="/analysis/{{ get_post_field('post_name', get_the_ID()) }}"
      class="block bg-white rounded-xl shadow-sm p-5 space-y-3 group
             transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg
             focus:outline-none focus:ring-2 focus:ring-brand-primary/30">

      <div class="font-semibold group-hover:text-brand-primary transition-colors">
        {{ $primaryMarket?->name }}
      </div>

      @if ($image = get_field('chart_image'))
      <img src="{{ dashboard_secure_media_url($image['ID'] ?? $image['id'] ?? 0, 'medium') }}" class="rounded-md">
      @endif

      <p class="text-sm text-slate-600">
        {{ mb_strimwidth(strip_tags(get_field('content_text')), 0, 220, '…') }}
      </p>

      <div class="text-sm text-slate-500">
        {{ date_i18n('d M, Y', strtotime(get_field('publish_date'))) }}
      </div>

      <span class="inline-block text-sm text-brand-primary font-medium transition-transform duration-200 group-hover:translate-x-1">
        View Report →
      </span>

    </a>
    @endwhile
    @php wp_reset_postdata(); @endphp
  </div>
  @endif

  @if ($view === 'list')

  <div class="bg-white rounded-xl shadow-sm divide-y overflow-hidden">
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

    <a href="/analysis/{{ get_post_field('post_name', get_the_ID()) }}"
      class="px-6 py-4 flex items-center justify-between group
             transition-all duration-200 hover:bg-slate-50
             focus:outline-none focus:ring-2 focus:ring-brand-primary/30">

      <div>
        <div class="font-medium group-hover:text-brand-primary transition-colors">
          {{ $primaryMarket?->name }}
        </div>
        <div class="text-sm text-slate-500">
          {{ date_i18n('d M, Y', strtotime(get_field('publish_date'))) }}
        </div>
      </div>

      <span class="text-sm text-brand-primary transition-transform duration-200 group-hover:translate-x-1">
        View →
      </span>

    </a>
    @endwhile
    @php wp_reset_postdata(); @endphp
  </div>
  @endif

</section>

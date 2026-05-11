<section class="max-w-7xl space-y-12">

  {{-- ========================= --}}
  {{-- LETZTE ANALYSEN --}}
  {{-- ========================= --}}
  <div>

    <header class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-semibold">Latest Analyses</h2>
      <a href="/dashboard-reports"
        class="text-sm text-brand-primary font-medium">
        View all →
      </a>
    </header>

    @php
    $analysisQuery = wp_cache_get('dashboard_latest_analyses');

    if (!$analysisQuery) {
    $analysisQuery = new WP_Query([
    'post_type' => 'analysis',
    'posts_per_page' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
    ]);
    wp_cache_set('dashboard_latest_analyses', $analysisQuery, '', 60);
    }
    @endphp

    <div class="space-y-4">
      @while($analysisQuery->have_posts())
      @php
      $analysisQuery->the_post();

      $post_id = get_the_ID();

      $image_id = get_post_meta($post_id, 'chart_image', true);
      $image_url = $image_id
      ? wp_get_attachment_image_url($image_id, 'thumbnail')
      : null;

      $publish_date = get_post_meta($post_id, 'publish_date', true);
      @endphp

      <a
        href="/analysis/{{ get_post_field('post_name', $post_id) }}"
        class="dashboard-card group rounded-xl p-5 flex items-center justify-between gap-4
         transition-[background-color,box-shadow,transform] duration-300 ease-in-out cursor-pointer
         hover:bg-slate-50/80 hover:-translate-y-0.5 hover:shadow-lg
         focus:outline-none focus:ring-2 focus:ring-brand-primary/40">

        <div class="flex items-center gap-4 min-w-0">
          @if($image_url)
          <img src="{{ $image_url }}"
            class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
          @endif

          <div class="min-w-0">
            <div class="break-all line-clamp-2 font-medium text-heading group-hover:text-brand-primary transition-colors duration-300 ease-in-out">
              {{ get_the_title() }}
            </div>
            <div class="text-sm text-meta">
              {{ $publish_date ? date_i18n('d.m.Y', strtotime($publish_date)) : get_the_date('d.m.Y') }}
            </div>
          </div>
        </div>

        <span class="text-sm btn btn-primary btn-sm whitespace-nowrap">
          View →
        </span>

      </a>

      @endwhile
      @php wp_reset_postdata(); @endphp
    </div>

  </div>


  {{-- ========================= --}}
  {{-- LETZTE PODCASTS --}}
  {{-- ========================= --}}
  <div>

    <header class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-semibold">Latest Podcasts</h2>
      <a href="/dashboard-media"
        class="text-sm text-brand-primary font-medium">
        View all →
      </a>
    </header>

    @php
    $mediaQuery = wp_cache_get('dashboard_latest_media');

    if (!$mediaQuery) {
    $mediaQuery = new WP_Query([
    'post_type' => 'media_entry',
    'posts_per_page' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
    ]);
    wp_cache_set('dashboard_latest_media', $mediaQuery, '', 60);
    }
    @endphp

    <div class="space-y-4">
      @while($mediaQuery->have_posts())
      @php
      $mediaQuery->the_post();

      $post_id = get_the_ID();

      $image_id = get_post_meta($post_id, 'cover_image', true);
      $coverUrl = $image_id
      ? wp_get_attachment_image_url($image_id, 'thumbnail')
      : get_theme_file_uri('resources/images/dashboard/default-cover.jpg');
      @endphp

      <a
        href="/dashboard-media"
        class="dashboard-card group rounded-xl p-5 flex items-center gap-4
         transition-all duration-300 ease-in-out cursor-pointer
         hover:bg-slate-50/80 hover:-translate-y-0.5 hover:shadow-lg
         focus:outline-none focus:ring-2 focus:ring-brand-primary/40">

        <img src="{{ $coverUrl }}"
          class="w-16 h-16 object-cover rounded-lg flex-shrink-0">

        <div class="flex-1 min-w-0">
          <div class="break-all line-clamp-2 font-medium text-heading group-hover:text-brand-primary transition-colors">
            {{ get_the_title() }}
          </div>
          <div class="text-sm text-meta">
            {{ get_the_date('d.m.Y') }}
          </div>
        </div>

        <span class="text-sm whitespace-nowrap
        btn btn-primary btn-sm">
          View →
        </span>

      </a>

      @endwhile
      @php wp_reset_postdata(); @endphp
    </div>

  </div>

</section>

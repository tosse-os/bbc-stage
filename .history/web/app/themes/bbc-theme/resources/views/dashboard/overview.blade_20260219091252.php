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
    $analysisQuery = new WP_Query([
    'post_type' => 'analysis',
    'posts_per_page' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
    ]);
    @endphp

    <div class="space-y-4">
      @while($analysisQuery->have_posts())
      @php
      $analysisQuery->the_post();
      $image_id = get_post_meta(get_the_ID(), 'chart_image', true);
      $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : null;
      @endphp

      <div class="dashboard-card rounded-xl p-5 flex items-center justify-between">

        <div class="flex items-center gap-4">
          @if($image)
          <img src="{{ $image['sizes']['thumbnail'] }}"
            class="w-16 h-16 object-cover rounded-lg">
          @endif

          <div>
            <div class="font-medium text-heading">
              {{ get_the_title() }}
            </div>
            <div class="text-sm text-meta">
              {{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}
            </div>
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
    $mediaQuery = new WP_Query([
    'post_type' => 'media_entry',
    'posts_per_page' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
    ]);
    @endphp

    <div class="space-y-4">
      @while($mediaQuery->have_posts())
      @php
      $mediaQuery->the_post();
      $image = get_field('cover_image');
      $coverUrl = $image
      ? $image['sizes']['thumbnail']
      : get_theme_file_uri('resources/images/dashboard/default-cover.jpg');
      @endphp

      <div class="dashboard-card rounded-xl p-5 flex items-center gap-4">

        <img src="{{ $coverUrl }}"
          class="w-16 h-16 object-cover rounded-lg">

        <div class="flex-1">
          <div class="font-medium text-heading">
            {{ get_the_title() }}
          </div>
          <div class="text-sm text-meta">
            {{ get_the_date('d.m.Y') }}
          </div>
        </div>

        <a href="/dashboard-media"
          class="text-sm text-brand-primary">
          View →
        </a>

      </div>

      @endwhile
      @php wp_reset_postdata(); @endphp
    </div>

  </div>

</section>

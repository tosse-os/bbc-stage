<section class="max-w-7xl space-y-12">

  {{-- ========================= --}}
  {{-- LETZTE ANALYSEN --}}
  {{-- ========================= --}}



  {{-- ========================= --}}
  {{-- LETZTE PODCASTS --}}
  {{-- ========================= --}}
  <div>



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

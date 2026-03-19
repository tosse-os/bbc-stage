<section class="space-y-6">

  <header>
    <h1 class="text-2xl font-semibold">
      Analysen
    </h1>
  </header>

  @include('dashboard.partials.filters')

  @php
  $query = new WP_Query([
  'post_type' => 'analysis',
  'posts_per_page' => 20,
  'orderby' => 'meta_value',
  'meta_key' => 'publish_date',
  'order' => 'DESC',
  ]);
  @endphp

  @if ($query->have_posts())
  <div class="space-y-4">
    @while ($query->have_posts())
    @php $query->the_post(); @endphp

    <article class="bg-dashboard-card rounded-xl p-6 flex items-center justify-between">
      <div class="flex items-center gap-6">

        @if ($image = get_field('chart_image'))
        <img
          src="{{ $image['sizes']['medium'] }}"
          alt=""
          class="w-32 h-auto rounded-lg">
        @endif

        <div>
          <h2 class="text-lg font-medium">
            {{ get_the_title() }}
          </h2>

          <p class="text-dashboard-muted text-sm mt-1">
            {{ get_field('short_description') }}
          </p>

          <p class="text-xs text-dashboard-muted mt-2">
            {{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}
          </p>
        </div>
      </div>

      <a
        href="{{ get_permalink() }}"
        class="inline-flex items-center px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary-dark transition">
        Analyse öffnen
      </a>
    </article>

    @endwhile
  </div>

  @php wp_reset_postdata(); @endphp
  @else
  <p class="text-dashboard-muted">
    Keine Analysen gefunden.
  </p>
  @endif

</section>

<section class="max-w-6xl">

  <header class="mb-8">
    <h1 class="text-3xl font-semibold text-gray-900">
      Analysen
    </h1>
  </header>

  <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    @include('dashboard.partials.filters')
  </div>

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

    <article class="bg-white rounded-xl shadow-sm border p-6 flex gap-6 items-center">

      @if ($image = get_field('chart_image'))
      <div class="w-48 flex-shrink-0">
        <img
          src="{{ $image['sizes']['medium'] }}"
          alt=""
          class="rounded-lg w-full h-auto">
      </div>
      @endif

      <div class="flex-1">
        <h2 class="text-lg font-semibold text-gray-900">
          {{ get_the_title() }}
        </h2>

        <p class="text-sm text-gray-600 mt-2">
          {{ get_field('short_description') }}
        </p>

        <p class="text-xs text-gray-400 mt-3">
          {{ date_i18n('d.m.Y', strtotime(get_field('publish_date'))) }}
        </p>
      </div>

      <div class="ml-auto">
        <a
          href="{{ get_permalink() }}"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 text-white text-sm hover:bg-slate-800 transition">
          Analyse öffnen
        </a>
      </div>

    </article>

    @endwhile

  </div>

  @php wp_reset_postdata(); @endphp
  @else
  <div class="bg-white rounded-xl p-6 text-gray-500">
    Keine Analysen gefunden.
  </div>
  @endif

</section>

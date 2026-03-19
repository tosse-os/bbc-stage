<section class="max-w-7xl">

  <header class="mb-6">
    <h1 class="text-2xl font-semibold">
      Analyses Overview
    </h1>
  </header>

  <div class="flex gap-2 mb-6 text-sm">
    <button class="px-4 py-2 rounded-md border bg-white">All</button>
    <button class="px-4 py-2 rounded-md text-slate-500 hover:text-slate-900">United Health</button>
    <button class="px-4 py-2 rounded-md text-slate-500 hover:text-slate-900">Air BNB</button>
    <button class="px-4 py-2 rounded-md text-slate-500 hover:text-slate-900">Coinbase</button>
    <button class="px-4 py-2 rounded-md text-slate-500 hover:text-slate-900">ASML</button>
  </div>

  <div class="bg-white rounded-xl shadow-sm">

    <div class="grid grid-cols-[240px_1fr_140px_160px] px-6 py-4 border-b text-sm font-medium text-slate-500">
      <div>Market</div>
      <div>Short Description</div>
      <div>Date</div>
      <div></div>
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
    <div class="divide-y">
      @while ($query->have_posts())
      @php $query->the_post(); @endphp

      <div class="grid grid-cols-[240px_1fr_140px_160px] px-6 py-5 items-center gap-4">

        <div class="font-medium">
          {{ get_field('market') ?: get_the_title() }}
        </div>

        <div class="flex items-center gap-4">
          @if ($image = get_field('chart_image'))
          <img src="{{ $image['sizes']['thumbnail'] }}" class="w-20 rounded-md">
          @endif
          <p class="text-sm text-slate-600">
            {{ get_field('short_description') }}
          </p>
        </div>

        <div class="text-sm text-slate-500">
          {{ date_i18n('d M, Y', strtotime(get_field('publish_date'))) }}
        </div>

        <div>
          <a href="{{ get_permalink() }}"
            class="inline-flex items-center px-4 py-2 rounded-md bg-slate-100 hover:bg-slate-200 text-sm">
            View Report →
          </a>
        </div>

      </div>

      @endwhile
    </div>

    @php wp_reset_postdata(); @endphp
    @else
    <div class="px-6 py-6 text-slate-500">
      No analyses found.
    </div>
    @endif

  </div>

</section>

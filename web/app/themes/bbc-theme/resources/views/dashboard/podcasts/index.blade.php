<section class="max-w-7xl">

  <header class="mb-6">
    <h1 class="text-lg md:text-2xl font-semibold">
      {{ dashboard_t('media.title') }}
    </h1>
  </header>

  @php
  $query = new WP_Query([
  'post_type' => 'podcast',
  'posts_per_page' => 30,
  'orderby' => 'date',
  'order' => 'DESC',
  ]);
  @endphp

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    @while ($query->have_posts())
    @php $query->the_post(); @endphp

    <div class="bg-white rounded-xl shadow-sm p-5 space-y-4">

      <h3 class="font-semibold text-slate-900">
        {{ get_the_title() }}
      </h3>

      <div class="text-sm text-slate-500">
        {{ get_the_date('d.m.Y') }}
      </div>

      @if ($audio = get_field('audio_file'))
      <audio controls class="w-full">
        <source src="{{ $audio['url'] }}" type="{{ $audio['mime_type'] }}">
      </audio>
      @endif

    </div>

    @endwhile

    @php wp_reset_postdata(); @endphp

  </div>

</section>

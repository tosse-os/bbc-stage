<section class="max-w-7xl">

  <header class="mb-8">
    <h1 class="text-xl md:text-2xl font-semibold text-white tracking-tight">
      Daily Podcasts
    </h1>
  </header>

  @php
  $query = new WP_Query([
  'post_type' => 'media_entry',
  'posts_per_page' => 30,
  'orderby' => 'date',
  'order' => 'DESC',
  ]);
  @endphp

  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

    @while ($query->have_posts())
    @php
    $query->the_post();

    $image = get_field('cover_image');
    $coverUrl = $image
    ? $image['sizes']['medium']
    : get_theme_file_uri('resources/images/dashboard/default-cover.jpg');

    $cleanTitle = html_entity_decode(get_the_title(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $mediaType = get_field('media_type');
    @endphp

    <div class="relative group p-[1px] rounded-3xl transition-all duration-500 hover:scale-[1.01]">
      <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-white/10 to-transparent opacity-30"></div>

      <div class="relative bg-slate-900/60 backdrop-blur-xl rounded-[23px] p-5 border border-white/5 shadow-2xl flex flex-col md:flex-row items-stretch gap-6">

        {{-- Cover --}}
        <div class="relative w-full md:w-36 flex-shrink-0">
          <img src="{{ $coverUrl }}"
            alt="{{ $image['alt'] ?? $cleanTitle }}"
            class="w-full h-full min-h-[110px] object-cover rounded-2xl shadow-lg">
        </div>

        {{-- Content --}}
        <div class="flex-1 flex flex-col justify-between py-1">

          <div class="mb-3">
            <h3 class="font-bold text-base md:text-lg text-white group-hover:text-brand-primary transition-colors leading-tight line-clamp-1">
              {!! $cleanTitle !!}
            </h3>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-[10px] font-bold text-brand-primary uppercase tracking-widest">
                {{ get_field('episode_label') ?: 'EPISODE NEW' }}
              </span>
              <span class="text-slate-600">•</span>
              <span class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">
                {{ get_the_date('d. F Y') }}
              </span>
            </div>
          </div>

          {{-- MEDIA RENDERING --}}
          @if($mediaType === 'audio')
          @include('dashboard.media-entry.card-audio')
          @elseif($mediaType === 'video')
          @include('dashboard.media-entry.card-video')
          @endif

        </div>
      </div>
    </div>

    @endwhile
    @php wp_reset_postdata(); @endphp

  </div>

</section>

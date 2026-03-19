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
    @php $query->the_post(); @endphp

    @php
    $image = get_field('cover_image');
    $coverUrl = $image
    ? $image['sizes']['medium']
    : get_theme_file_uri('resources/images/dashboard/default-cover.jpg');

    $cleanTitle = html_entity_decode(get_the_title(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    @endphp

    {{-- Glassmorphism Player Card --}}
    <div class="relative group p-[1px] rounded-3xl transition-all duration-500 hover:scale-[1.01]">
      <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-white/10 to-transparent opacity-30"></div>

      <div class="relative bg-slate-900/60 backdrop-blur-xl rounded-[23px] p-5 border border-white/5 shadow-2xl flex flex-col md:flex-row items-stretch gap-6">

        {{-- Linke Seite: Cover Image - Höhe jetzt flexibel (h-auto) passend zum Content --}}
        <div class="relative w-full md:w-36 flex-shrink-0">
          <img src="{{ $coverUrl }}"
            alt="{{ $image['alt'] ?? $cleanTitle }}"
            class="w-full h-full min-h-[110px] object-cover rounded-2xl shadow-lg">
        </div>

        {{-- Rechte Seite: Content & Player --}}
        <div class="flex-1 flex flex-col justify-between py-1">

          {{-- Titel Bereich mit besserem Zeilenabstand --}}
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

          @if ($audio = get_field('audio_file'))
          {{-- Player-Balken: Kompakter & bündig mit Bildunterkante --}}
          <div class="custom-audio-wrapper bg-slate-800/40 border border-white/5 rounded-2xl px-4 py-2.5 flex items-center gap-4 select-none">
            <audio src="{{ $audio['url'] }}" class="hidden-audio" preload="metadata"></audio>

            {{-- Play Button --}}
            <button class="play-btn w-9 h-9 flex-shrink-0 flex items-center justify-center bg-brand-primary rounded-full text-white hover:scale-105 active:scale-95 transition shadow-lg">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
              </svg>
            </button>

            {{-- Progress Area: Jetzt maximal breit --}}
            <div class="flex-1 flex flex-col">
              <div class="flex justify-between text-[9px] font-bold text-slate-300 mb-1 opacity-80">
                <span class="current-time">0:00</span>
                <span class="duration">0:00</span>
              </div>
              <input type="range"
                class="seek-bar w-full h-1 accent-brand-primary cursor-pointer appearance-none bg-slate-700 rounded-lg"
                value="0" step="0.1">
            </div>

            {{-- Zusatzelemente: Speed & Mute --}}
            <div class="flex items-center gap-3 ml-2">
              <button class="speed-btn text-[10px] font-bold text-slate-400 hover:text-white transition">1x</button>
              <button class="mute-btn text-slate-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                </svg>
              </button>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>


    @endwhile

    @php wp_reset_postdata(); @endphp

  </div>

</section>

<style>
  /* Styling für den Kapsel-Audio-Player aus dem Mockup */
  .custom-audio-player {
    filter: invert(100%) hue-rotate(180deg) brightness(1.5);
    /* Macht den Player weiß */
    border-radius: 50px;
    background: #ffffff;
  }

  /* Chrome/Safari spezifische Anpassungen für das Kapsel-Design */
  .custom-audio-player::-webkit-media-controls-panel {
    background-color: #ffffff;
  }
</style>

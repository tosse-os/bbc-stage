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

    {{-- Glassmorphism Card --}}
    <div class="relative group p-[1px] rounded-3xl transition-all duration-500 hover:scale-[1.02]">
      {{-- Subtiler Gradient-Border Effekt --}}
      <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-white/20 to-transparent opacity-50"></div>

      <div class="relative bg-slate-900/40 backdrop-blur-xl rounded-[23px] p-6 border border-white/10 shadow-2xl group">

        <div class="mb-6">
          <h3 class="font-bold text-lg text-white group-hover:text-brand-primary transition-colors">
            {!! html_entity_decode(get_the_title(), ENT_QUOTES | ENT_HTML5, 'UTF-8') !!}
          </h3>
          <div class="text-xs font-medium text-slate-400 uppercase tracking-widest">
            {{ get_the_date('d.m.Y') }}
          </div>
        </div>

        @if ($audio = get_field('audio_file'))

        <div class="custom-audio-wrapper bg-white rounded-full px-4 py-2 flex items-center gap-3 select-none">
          <audio src="{{ $audio['url'] }}" class="hidden-audio" preload="metadata"></audio>

          <button class="play-btn w-8 h-8 flex-shrink-0 flex items-center justify-center bg-brand-primary rounded-full text-white hover:scale-110 transition">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M8 5v14l11-7z" />
            </svg>
          </button>

          <div class="flex-1 flex flex-col justify-center">
            <div class="flex justify-between text-[10px] font-bold text-slate-400 mb-0.5">
              <span class="current-time">0:00</span>
              <span class="duration">0:00</span>
            </div>
            {{-- Die Trackline als interaktive Range --}}
            <input type="range" class="seek-bar w-full h-1 accent-brand-primary cursor-pointer" value="0" step="0.1">
          </div>

          {{-- Interaktiver Lautsprecher --}}
          <button class="mute-btn text-slate-400 hover:text-brand-primary transition">
            <svg class="w-5 h-5 speaker-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
            </svg>
          </button>
        </div>

        @endif
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

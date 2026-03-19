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
    // Bild-Logik: ACF-Feld oder Standardbild
    $image = get_field('cover_image');
    $coverUrl = $image
    ? $image['sizes']['medium']
    : Vite::asset('resources/images/dashboard/default-cover.jpg');

    // Titel-Bereinigung für die Anzeige
    $cleanTitle = html_entity_decode(get_the_title(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    @endphp

    {{-- Glassmorphism Player Card --}}
    <div class="relative group p-[1px] rounded-3xl transition-all duration-500 hover:scale-[1.01]">
      {{-- Subtiler Gradient-Border Effekt --}}
      <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-white/20 to-transparent opacity-50"></div>

      <div class="relative bg-slate-900/40 backdrop-blur-xl rounded-[23px] p-5 border border-white/10 shadow-2xl flex flex-col md:flex-row gap-6">

        {{-- Linke Seite: Cover Image (Inspiration von Ihren Screenshots) --}}
        <div class="relative w-full md:w-32 h-32 flex-shrink-0">
          <img src="{{ $coverUrl }}"
            alt="{{ $cleanTitle }}"
            class="w-full h-full object-cover rounded-2xl shadow-lg border border-white/10">

          {{-- Optionaler Overlay-Playbutton auf dem Bild --}}
          <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="bg-brand-primary/80 backdrop-blur-sm p-2 rounded-full text-white">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
              </svg>
            </div>
          </div>
        </div>

        {{-- Rechte Seite: Content & Player --}}
        <div class="flex-1 flex flex-col justify-between">

          {{-- Header: Titel & Info --}}
          <div class="mb-4">
            <h3 class="font-bold text-lg text-white group-hover:text-brand-primary transition-colors line-clamp-1">
              {!! $cleanTitle !!}
            </h3>
            <div class="flex items-center gap-2 mt-1">
              <span class="text-[10px] font-bold text-brand-primary uppercase tracking-widest">
                Episode {{ get_field('episode_number') ?: 'New' }}
              </span>
              <span class="text-slate-500">•</span>
              <span class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">
                {{ get_the_date('d. F Y') }}
              </span>
            </div>
          </div>

          {{-- Audio Player Section --}}
          @if ($audio = get_field('audio_file'))
          <div class="custom-audio-wrapper bg-white/95 rounded-2xl px-4 py-3 flex items-center gap-4 select-none shadow-inner">
            <audio src="{{ $audio['url'] }}" class="hidden-audio" preload="metadata"></audio>

            {{-- Play/Pause Button --}}
            <button class="play-btn w-10 h-10 flex-shrink-0 flex items-center justify-center bg-brand-primary rounded-full text-white hover:scale-105 active:scale-95 transition shadow-md">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z" />
              </svg>
            </button>

            {{-- Progress & Time --}}
            <div class="flex-1 flex flex-col">
              <div class="flex justify-between text-[10px] font-bold text-slate-500 mb-1">
                <span class="current-time">0:00</span>
                <span class="duration">0:00</span>
              </div>
              <input type="range"
                class="seek-bar w-full h-1.5 accent-brand-primary cursor-pointer appearance-none bg-slate-200 rounded-lg overflow-hidden"
                value="0" step="0.1">
            </div>

            {{-- Speed Control (wie in Ihrem Screenshot "1x") --}}
            <button class="speed-btn text-[10px] font-bold text-slate-400 hover:text-brand-primary transition w-6">
              1x
            </button>

            {{-- Mute/Volume --}}
            <button class="mute-btn text-slate-400 hover:text-brand-primary transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
              </svg>
            </button>
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

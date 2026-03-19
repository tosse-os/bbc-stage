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

      <div class="relative bg-slate-900/40 backdrop-blur-xl rounded-[23px] p-6 h-full flex flex-col justify-between border border-white/10 shadow-2xl">

        <div class="space-y-2 mb-6">
          <h3 class="font-bold text-lg text-white leading-snug group-hover:text-brand-primary transition-colors">
            {{ get_the_title() }}
          </h3>

          <div class="text-xs font-medium text-slate-400 uppercase tracking-widest">
            {{ get_the_date('d.m.Y') }}
          </div>
        </div>

        @if ($audio = get_field('audio_file'))
        <div class="audio-player-wrapper">
          {{-- Der native Player wird via CSS (siehe unten) an das weiße Kapsel-Design angepasst --}}
          <audio controls class="custom-audio-player w-full h-10">
            <source src="{{ $audio['url'] }}" type="{{ $audio['mime_type'] }}">
          </audio>
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

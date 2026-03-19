<div class="relative bg-slate-900/40 backdrop-blur-xl rounded-[23px] p-6 border border-white/10 shadow-2xl group">

  <div class="mb-6">
    <h3 class="font-bold text-lg text-white group-hover:text-brand-primary transition-colors">
      {{ get_the_title() }}
    </h3>
    <div class="text-xs font-medium text-slate-400 uppercase tracking-widest">
      {{ get_the_date('d.m.Y') }}
    </div>
  </div>

  @if ($audio = get_field('audio_file'))
  <div class="custom-audio-wrapper bg-white rounded-full px-4 py-2 flex items-center gap-3 select-none">
    <audio src="{{ $audio['url'] }}" class="hidden-audio"></audio>

    {{-- Play/Pause Button --}}
    <button class="play-btn w-8 h-8 flex items-center justify-center bg-brand-primary rounded-full text-white hover:scale-110 transition">
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
        <path d="M8 5v14l11-7z" />
      </svg>
    </button>

    {{-- Zeit & Progress --}}
    <div class="flex-1 flex flex-col">
      <div class="flex justify-between text-[10px] font-bold text-slate-400 mb-1">
        <span class="current-time">0:00</span>
        <span class="duration">0:00</span>
      </div>
      <div class="h-1 bg-slate-100 rounded-full relative overflow-hidden">
        <div class="progress-bar absolute h-full bg-brand-primary w-0"></div>
      </div>
    </div>

    {{-- Volume --}}
    <div class="text-slate-400 hover:text-brand-primary cursor-pointer">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
      </svg>
    </div>
  </div>
  @endif
</div>

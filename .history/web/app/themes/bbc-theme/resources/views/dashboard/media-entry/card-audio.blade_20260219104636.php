@php $audio = get_field('audio_file'); @endphp

@if($audio)
<div class="custom-audio-wrapper bg-slate-800/40 border border-white/5 rounded-2xl px-4 py-2.5 flex items-center gap-4 select-none">

  <audio src="{{ $audio['url'] }}" class="hidden-audio" preload="metadata"></audio>

  <button class="play-btn w-9 h-9 flex-shrink-0 flex items-center justify-center bg-brand-primary rounded-full text-white hover:scale-105 active:scale-95 transition shadow-lg">
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
      <path d="M8 5v14l11-7z" />
    </svg>
  </button>

  <div class="flex-1 flex flex-col">
    <div class="flex justify-between text-[9px] font-bold text-slate-300 mb-1 opacity-80">
      <span class="current-time">0:00</span>
      <span class="duration">0:00</span>
    </div>
    <input type="range"
      class="seek-bar w-full h-1 accent-brand-primary cursor-pointer appearance-none bg-slate-700 rounded-lg"
      value="0" step="0.1">
  </div>

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

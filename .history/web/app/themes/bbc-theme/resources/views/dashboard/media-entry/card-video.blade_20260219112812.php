@php $videoUrl = get_field('video_url'); @endphp

@if($videoUrl)
<button type="button"
  class="video-trigger w-full rounded-2xl overflow-hidden bg-slate-800/40 border border-white/5 flex items-center px-4 py-2 gap-4"
  data-video-url="{{ esc_url($videoUrl) }}">

  <div class="w-9 h-9 flex-shrink-0 flex items-center justify-center bg-brand-primary rounded-full text-white shadow-lg">
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
      <path d="M8 5v14l11-7z" />
    </svg>
  </div>

  <div class="flex-1">
    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">
      Video ansehen
    </span>
    <div class="h-1 w-full bg-slate-700 rounded-full mt-1 overflow-hidden">
      <div class="bg-brand-primary h-full w-1/3 opacity-50"></div>
    </div>
  </div>

  <span class="text-[10px] font-bold text-brand-primary uppercase">
    Abspielen
  </span>

</button>
@endif

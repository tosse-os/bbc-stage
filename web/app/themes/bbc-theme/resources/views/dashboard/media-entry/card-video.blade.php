@php $videoUrl = get_field('video_url'); @endphp

@if($videoUrl)
<div class="video-mini-player rounded-2xl overflow-hidden bg-slate-800/40 border border-white/5 flex items-center px-4 py-2 gap-4">

  <button
    class="video-trigger w-9 h-9 flex-shrink-0 flex items-center justify-center bg-brand-primary rounded-full text-white shadow-lg"
    data-video="{{ esc_url($videoUrl) }}">
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
      <path d="M21 6.5l-6 4.5v-4.5h-13v11h13v-4.5l6 4.5v-11z" />
    </svg>
  </button>

  <div class="flex-1">
    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">
      {{ dashboard_t('media.watch_video') }}
    </span>
    <div class="h-1 w-full bg-slate-700 rounded-full mt-1 overflow-hidden">
      <div class="bg-brand-primary h-full w-1/3 opacity-50"></div>
    </div>
  </div>

  <button
    class="video-trigger text-[10px] font-bold text-brand-primary hover:text-white transition uppercase"
    data-video="{{ esc_url($videoUrl) }}">
    {{ dashboard_t('media.play') }}
  </button>

</div>
@endif

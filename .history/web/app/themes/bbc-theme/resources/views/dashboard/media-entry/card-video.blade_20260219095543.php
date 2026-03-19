@php
$videoUrl = get_field('video_url');
@endphp

@if($videoUrl)
<div class="rounded-2xl overflow-hidden bg-slate-800/40 border border-white/5">
  <div class="aspect-video">
    {!! wp_oembed_get($videoUrl) !!}
  </div>
</div>
@endif

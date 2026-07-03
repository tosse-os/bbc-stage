<div id="video-modal"
  class="fixed inset-0 z-50 hidden items-center justify-center">

  <div class="absolute inset-0 bg-black/70 backdrop-blur-sm opacity-0 transition-opacity duration-300"
    data-video-overlay></div>

  <div class="relative z-10 w-full max-w-5xl mx-auto px-4 scale-95 opacity-0 transition-all duration-300"
    data-video-dialog>

    <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl">

      <button type="button"
        data-video-close
        aria-label="{{ dashboard_t('media.close') }}"
        class="absolute top-3 right-3 z-20 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 transition text-white flex items-center justify-center">
        ✕
      </button>

      <div class="aspect-video bg-black">
        <div id="video-modal-content" class="w-full h-full"></div>
      </div>

    </div>
  </div>

</div>

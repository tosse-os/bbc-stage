<footer class="content-info bg-slate-900 text-white">
  <div class="container-content py-16">
    {{-- @php(dynamic_sidebar('sidebar-footer')) --}}
  </div>

  {{-- Scroll-to-Top Button --}}
  <button
    id="scrollToTopBtn"
    class="fixed bottom-6 right-6 z-50 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-primary text-white shadow-[0_2px_6px_rgba(0,0,0,0.15)] opacity-0 transition-[opacity,background-color] duration-300 hover:bg-brand-primaryHover cursor-pointer">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
  </button>
</footer>

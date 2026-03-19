<footer class="relative overflow-hidden bg-slate-950 text-slate-300">
  <div class="absolute inset-0">
    <img
      src="{{ Vite::asset('resources/images/footer-bg.jpg') }}"
      alt=""
      class="h-full w-full object-cover opacity-60">
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/20 via-slate-950/10 to-slate-950/20"></div>
  </div>

  <div class="relative">
    <div class="container-content py-20">
      <div class="grid grid-cols-1 gap-16 lg:grid-cols-2">

        <div>
          <div class="mb-6 flex items-center gap-3">
            <img
              src="{{ Vite::asset('resources/images/bridge-free.png') }}"
              alt="Bloombridge Capital"
              class="h-12">
          </div>

          <div class="text-sm text-slate-400">
            Follow Us
          </div>

          <div class="mt-4 flex gap-4">
            <a href="#" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-300 hover:border-brand-primary hover:text-white transition">
              @include('icons.x')
            </a>
            <a href="#" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-700 text-slate-300 hover:border-brand-primary hover:text-white transition">
              @include('icons.instagram')
            </a>
            <a href="#" class="flex h-10 w-10 items-center justify-center rounded-full text-slate-300 hover:border-brand-primary hover:text-white transition">
              @include('icons.youtube')
            </a>
            <a href="#" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-700 text-slate-300 hover:border-brand-primary hover:text-white transition">
              IN
            </a>
          </div>

          <div class="mt-10 text-xs text-slate-500">
            © {{ date('Y') }} Bloombridge Capital. All Rights Reserved
          </div>
        </div>

        <div class="flex flex-col items-start gap-3 text-sm lg:items-end">
          <a href="#about" class="hover:text-white transition">About</a>
          <a href="#about" class="hover:text-white transition">Services</a>
          <a href="#contact" class="hover:text-white transition">Contact</a>

          <div class="mt-8 flex gap-4 text-xs text-slate-500">
            <a href="/privacy-policy" class="hover:text-white transition">Privacy policy</a>
            <span>·</span>
            <a href="/terms" class="hover:text-white transition">Terms of use</a>
          </div>
        </div>

      </div>
    </div>

    <button
      id="scrollToTopBtn"
      class="fixed bottom-6 right-6 z-50 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-primary text-white shadow-[0_2px_6px_rgba(0,0,0,0.15)] opacity-0 transition-[opacity,background-color] duration-300 hover:bg-brand-primaryHover cursor-pointer">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
      </svg>
    </button>
  </div>
</footer>

<header class="sticky top-0 z-50 backdrop-blur-md bg-brand-dark/30 border-b border-white/5">
  <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-8">
    <div class="flex items-center gap-3">
      <img src="{{ Vite::asset('resources/images/bridge-free.png') }}" alt="Bloombridge Capital" class="h-6 opacity-90">
    </div>

    <nav class="hidden items-center gap-12 text-sm text-slate-300 lg:flex">
      <a class="hover:text-white transition">Home</a>
      <a class="hover:text-white transition">About</a>
      <a class="hover:text-white transition">Contact & Support</a>
      <a class="hover:text-white transition">Team</a>
    </nav>

    <a
      href="/login"
      class="rounded-lg bg-brand-primary/90 px-5 py-2 text-sm font-medium text-white hover:bg-brand-primary transition">
      Log in
    </a>
  </div>
</header>

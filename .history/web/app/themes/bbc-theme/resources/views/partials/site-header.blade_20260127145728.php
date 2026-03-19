<header class="sticky top-0 z-50 bg-slate-950/95 backdrop-blur">
  <div class="mx-auto flex pt-[calc(theme(spacing.24)+theme(spacing.24))]
 max-w-7xl items-center justify-between px-8">
    <div class="flex items-center gap-3">
      <img
        src="{{ Vite::asset('resources/images/bridge-free.png') }}"
        alt="Bloombridge Capital"
        class="h-8 opacity-90">
    </div>

    <nav class="hidden items-center gap-10 text-sm text-slate-300 lg:flex">
      <a class="hover:text-white transition">Home</a>
      <a class="hover:text-white transition">About</a>
      <a class="hover:text-white transition">Contact & Support</a>
      <a class="hover:text-white transition">Team</a>
    </nav>

    <a
      href="/login"
      class="rounded-lg bg-brand-primary px-5 py-2 text-sm font-medium text-white hover:bg-cyan-500 transition">
      Log in
    </a>
  </div>
</header>

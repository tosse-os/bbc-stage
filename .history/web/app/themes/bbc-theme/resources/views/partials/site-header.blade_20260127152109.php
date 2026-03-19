<header class="fixed inset-x-0 top-0 z-50 bg-slate-950/40 backdrop-blur-sm">

  <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-8">
    <div class="flex items-center">
      <img
        src="{{ Vite::asset('resources/images/bridge-free.png') }}"
        alt="Bloombridge Capital"
        class="h-11">
    </div>

    <nav class="hidden items-center gap-8 text-sm text-slate-300 lg:flex">
      <a class="hover:text-white transition">Home</a>
      <a class="hover:text-white transition">About</a>
      <a class="hover:text-white transition">Contact & Support</a>
      <a class="hover:text-white transition">Team</a>
    </nav>

    <a
      href="/login"
      class="rounded-md bg-brand-primary px-6 py-2 text-sm font-medium text-white hover:bg-cyan-500 transition">
      Log in
    </a>
  </div>
</header>

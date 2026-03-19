<header class="absolute inset-x-0 top-0 z-50">
  <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-6">
    <div class="flex items-center gap-3">
      <img src="{{ Vite::asset('resources/images/bridge-free.png') }}" alt="Bloombridge Capital" class="h-8">
    </div>

    <nav class="hidden items-center gap-8 text-sm text-slate-200 lg:flex">
      <a href="#" class="hover:text-white">Home</a>
      <a href="#" class="hover:text-white">About</a>
      <a href="#" class="hover:text-white">Contact & Support</a>
      <a href="#" class="hover:text-white">Team</a>
    </nav>

    <div>
      <a
        href="/login"
        class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-medium text-white hover:bg-cyan-500 transition">
        Log in
      </a>
    </div>
  </div>
</header>

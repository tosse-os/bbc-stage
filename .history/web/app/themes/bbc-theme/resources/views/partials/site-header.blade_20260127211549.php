<header class="fixed inset-x-0 top-0 z-50 bg-slate-950/40 ">
  <div class="container-content">
    <div class="flex h-16 items-center justify-between">

      <div class="flex items-center">
        <img
          src="{{ Vite::asset('resources/images/bridge-free.png') }}"
          alt="Bloombridge Capital"
          class="h-13">
      </div>

      <nav class="hidden items-center gap-8 text-base text-slate-300 lg:flex">
        <a class="hover:text-white transition" href="#top">Home</a>
        <a class="hover:text-white transition" href="#about">About</a>
        <a class="hover:text-white transition" href="#contact">Contact & Support</a>
        <a class="hover:text-white transition" href="#team">Team</a>

        <div class="ml-4 flex items-center gap-3 text-sm font-medium uppercase tracking-wide text-white">
          @php($langs = pll_the_languages(['raw' => 1]))
          @foreach($langs as $lang)
          <a
            href="{{ $lang['url'] }}"
            class="{{ $lang['current_lang'] ? 'opacity-100' : 'opacity-60 hover:opacity-100 transition' }}">
            {{ strtoupper($lang['slug']) }}
          </a>
          @endforeach
        </div>
      </nav>

      <a
        href="/login"
        class="rounded-md bg-brand-primary px-6 py-2 text-sm font-medium text-white hover:bg-brand-primaryHover transition">
        Log in
      </a>

    </div>
  </div>
</header>

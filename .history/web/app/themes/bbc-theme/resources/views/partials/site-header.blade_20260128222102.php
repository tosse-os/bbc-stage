<header class="fixed inset-x-0 top-0 z-50 bg-slate-950/60 backdrop-blur">
  <div class="container-content">
    <div class="flex h-16 items-center justify-between">

      <div class="flex items-center">
        <img
          src="{{ Vite::asset('resources/images/bridge-free.png') }}"
          alt="Bloombridge Capital"
          class="h-10 lg:h-13">
      </div>

      <nav class="hidden items-center gap-8 text-base text-slate-300 lg:flex">
        <a class="transition hover:text-white" href="#top">Home</a>
        <a class="transition hover:text-white" href="#about">About</a>
        <a class="transition hover:text-white" href="#contact">Contact & Support</a>
        <a class="transition hover:text-white" href="#team">Team</a>
        <a class="transition hover:text-white" href="#market-insights">Insights</a>
      </nav>

      <div class="flex items-center gap-4">
        <a
          href="/login"
          class="hidden rounded-md bg-brand-primary px-6 py-2 text-sm font-medium text-white transition hover:bg-brand-primaryHover lg:inline-flex">
          Log in
        </a>

        <a
          href="/login"
          class="flex h-10 w-10 items-center justify-center rounded-md bg-brand-primary text-white lg:hidden">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20a8 8 0 0116 0" />
          </svg>
        </a>

        <button
          id="mobileMenuToggle"
          class="flex h-10 w-10 items-center justify-center text-white lg:hidden"
          aria-label="Open menu">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

    </div>
  </div>
</header>

<div
  id="mobileMenu"
  class="fixed inset-y-0 right-0 z-50 w-full max-w-sm translate-x-full bg-slate-950/70 text-slate-200 transition-transform duration-600">

  <div class="flex h-16 items-center justify-between px-6">
    <span class="text-sm font-semibold uppercase tracking-wide">Menu</span>
    <button id="mobileMenuClose" class="h-10 w-10 text-white">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>

  <nav class="mt-10 flex flex-col gap-6 px-6 text-lg">
    <a href="#top" class="mobile-link">Home</a>
    <a href="#about" class="mobile-link">About</a>
    <a href="#contact" class="mobile-link">Contact & Support</a>
    <a href="#team" class="mobile-link">Team</a>
    <a href="#market-insights" class="mobile-link">Insights</a>
  </nav>
</div>

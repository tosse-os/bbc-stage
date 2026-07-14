<header
  id="siteHeader"
  class="fixed inset-x-0 top-0 z-50 backdrop-blur_off transition-[background-color] duration-300">

  @php
  $landingBaseUrl = function_exists('pll_home_url')
  ? pll_home_url(function_exists('pll_current_language') ? pll_current_language('slug') : '')
  : home_url('/');
  @endphp

  <div class="container-content">
    <div class="flex h-16 items-center justify-between">

      {{-- Logo --}}
      <div class="flex items-center">
        <a href="{{ $landingBaseUrl }}#top">
          <img
            src="{{ Vite::asset('resources/images/landingpage/bridge-free.png') }}"
            alt="Bloombridge Capital"
            class="h-10 lg:h-13">
        </a>
      </div>

      {{-- Desktop Navigation --}}
      <nav class="hidden flex-1 justify-center gap-8 text-base text-slate-300 lg:flex">
        <a class="nav-link" href="{{ $landingBaseUrl }}#top">{{ pll__('Start') }}</a>
        <a class="nav-link" href="{{ $landingBaseUrl }}#market-insights">{{ pll__('Insights') }}</a>
        <a class="nav-link" href="{{ $landingBaseUrl }}#about">{{ pll__('Kompetenz') }}</a>
        <a class="nav-link" href="{{ $landingBaseUrl }}#team">Team</a>
        <a class="nav-link" href="{{ $landingBaseUrl }}#reviews">{{ pll__('Reviews') }}</a>
        <a class="nav-link" href="{{ $landingBaseUrl }}#contact">{{ pll__('Support') }}</a>
      </nav>

      {{-- Right Side Actions --}}
      <div class="flex items-center gap-4">

        {{-- Language Selector (Desktop) --}}
        <div class="hidden rounded-md bg-white/5 p-0.5 text-xs font-medium uppercase tracking-wide text-white lg:flex">
          @php($langs = pll_the_languages(['raw' => 1]))
          @foreach($langs as $lang)
          <a
            href="{{ $lang['url'] }}"
            class="px-2.5 py-1 rounded-sm transition
                {{ $lang['current_lang']
                  ? 'bg-white/15 text-white'
                  : 'bg-white/[0.06] text-white/70 hover:bg-white/[0.1] hover:text-white' }}">
            {{ strtoupper($lang['slug']) }}
          </a>
          @endforeach
        </div>

        {{-- Login Desktop --}}
        <a
          href="{{ home_url('/dashboard-login') }}"
          class="hidden rounded-md bg-brand-primary px-6 py-2 text-sm font-medium text-white transition hover:bg-brand-primaryHover lg:inline-flex">
          Log in
        </a>

        {{-- Hamburger Mobile --}}
        <button
          id="mobileMenuToggle"
          class="flex h-10 w-10 items-center justify-center text-white lg:hidden"
          aria-label="Open menu">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

      </div>
    </div>
  </div>
</header>

{{-- Off-Canvas Mobile Menu --}}
<div
  id="mobileMenu"
  class="fixed inset-y-0 right-0 z-50 w-[300px] translate-x-full bg-slate-950/85 text-slate-200 transition-transform duration-300">

  {{-- Off-Canvas Header --}}
  <div class="flex h-16 items-center justify-between px-6">
    <span class="text-sm font-semibold uppercase tracking-wide">Menu</span>
    <button id="mobileMenuClose" class="h-10 w-10 text-white" aria-label="Close menu">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
  </div>

  {{-- Language Selector Mobile --}}
  <div class="px-6 pt-2">
    <div class="inline-flex rounded-md bg-white/5 p-0.5 text-[11px] font-medium uppercase tracking-wide text-white">
      @foreach($langs as $lang)
      <a
        href="{{ $lang['url'] }}"
        class="px-2 py-1 rounded-sm transition
            {{ $lang['current_lang']
              ? 'bg-white/15 text-white'
              : 'bg-white/[0.06] text-white/70 hover:bg-white/[0.1] hover:text-white' }}">
        {{ strtoupper($lang['slug']) }}
      </a>
      @endforeach
    </div>
  </div>

  {{-- Mobile Navigation --}}
  <nav class="mt-10 flex flex-col gap-6 px-6 text-lg">
    <a href="{{ $landingBaseUrl }}#top" class="mobile-link">{{ pll__('Start') }}</a>
    <a href="{{ $landingBaseUrl }}#market-insights" class="mobile-link">{{ pll__('Insights') }}</a>
    <a href="{{ $landingBaseUrl }}#about" class="mobile-link">{{ pll__('Kompetenz') }}</a>
    <a href="{{ $landingBaseUrl }}#team" class="mobile-link">Team</a>
    <a href="{{ $landingBaseUrl }}#reviews" class="mobile-link">{{ pll__('Reviews') }}</a>
    <a href="{{ $landingBaseUrl }}#contact" class="mobile-link">{{ pll__('Support') }}</a>
  </nav>

  {{-- Login Mobile --}}
  <div class="mt-10 px-6">
    <a
      href="/dashboard-login"
      class="flex w-full items-center justify-center rounded-md bg-brand-primary px-4 py-2 text-base font-medium text-white">
      Log in
    </a>
  </div>
</div>

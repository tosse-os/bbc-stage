@if (is_user_logged_in())
@php
$user = wp_get_current_user();

$avatar_id = get_user_meta($user->ID, 'dashboard_avatar_id', true);
$avatar_url = $avatar_id
? wp_get_attachment_image_url($avatar_id, 'thumbnail')
: get_avatar_url($user->ID);
@endphp

{{-- Logo --}}
<div
  data-sidebar-logo
  class="sidebar-logo px-6 py-6 border-b border-slate-800 flex items-center justify-center">
  <img
    src="{{ get_theme_file_uri('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}"
    alt="Bloombridge Capital">
</div>

{{-- Language Selector (Sidebar) --}}
@php($langs = pll_the_languages(['raw' => 1]))

@if(!empty($langs))

<div class="md:mt-6 px-4 flex items-center justify-center">

  {{-- DESKTOP (ab md) → wie bisher abhängig vom Collapse-State --}}
  <div class="hidden md:flex">

    {{-- EXPANDED --}}
    <div class="sidebar-expanded max-w-[80px] flex rounded-lg bg-white/14 backdrop-blur-sm overflow-hidden text-xs font-semibold tracking-wide">
      @foreach($langs as $lang)
      <a
        href="{{ $lang['url'] }}"
        class="flex-1 text-center px-3 py-1.5 transition
          {{ $lang['current_lang']
            ? 'bg-brand-primary text-white'
            : 'text-[var(--text-secondary)] hover:bg-[var(--surface-card)] hover:text-[var(--text-primary)]' }}">
        {{ strtoupper($lang['slug']) }}
      </a>
      @endforeach
    </div>

    {{-- COLLAPSED --}}
    <div class="sidebar-collapsed hidden">
      @foreach($langs as $lang)
      @if(!$lang['current_lang'])
      <a
        href="{{ $lang['url'] }}"
        class="w-10 h-10 rounded-lg
                 flex items-center justify-center
                 text-xs font-semibold
                 bg-white/14 text-white
                 hover:bg-white/20 transition">
        {{ strtoupper($lang['slug']) }}
      </a>
      @endif
      @endforeach
    </div>

  </div>

  {{-- MOBILE (unter md) → immer nur andere Sprache --}}
  <div class="md:hidden">
    @foreach($langs as $lang)
    @if(!$lang['current_lang'])
    <a
      href="{{ $lang['url'] }}"
      class="w-10 h-10 rounded-lg
               flex items-center justify-center
               text-xs font-semibold
               bg-white/14 text-white
               hover:bg-white/20 transition">
      {{ strtoupper($lang['slug']) }}
    </a>
    @endif
    @endforeach
  </div>

</div>

@endif



{{-- THEME SWITCH --}}
<button
  type="button"
  data-theme-toggle
  data-ajax="{{ admin_url('admin-ajax.php') }}"
  data-nonce="{{ wp_create_nonce('dashboard_theme_toggle') }}"
  class="md:mt-4 mx-auto flex items-center justify-center
         w-10 h-10 rounded-lg
         text-slate-300 hover:text-white
         hover:bg-slate-800/50 transition"
  aria-label="Theme wechseln">

  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
    fill="none" stroke="currentColor" stroke-width="1.8"
    stroke-linecap="round" stroke-linejoin="round"
    class="w-5 h-5">
    <circle cx="12" cy="12" r="5"></circle>
    <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M16.95 16.95l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M16.95 7.05l1.42-1.42"></path>
  </svg>

</button>


{{-- Collapse Switch --}}
<button
  type="button"
  data-sidebar-toggle
  data-ajax="{{ admin_url('admin-ajax.php') }}"
  data-nonce="{{ wp_create_nonce('dashboard_sidebar_toggle') }}"
  aria-label="Sidebar ein- oder ausklappen"
  class="sidebar-toggle mx-auto my-3 flex items-center justify-center
         w-10 h-10 rounded-lg
         text-slate-300 hover:text-white
         hover:bg-slate-800/50 transition group">

  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    class="w-6 h-6 transition-transform duration-300 ease-in-out"
    data-sidebar-icon>
    <polyline points="13 17 8 12 13 7" />
    <polyline points="18 17 13 12 18 7" />
  </svg>


</button>


{{-- Navigation --}}
<nav class="flex-1 px-4 py-6 space-y-1 text-sm">

  <a href="/dashboard"
    class="nav-item flex items-center px-4 py-3 rounded-lg
  {{ dashboard_active('dashboard')
      ? 'bg-brand-primary/60 cursor-default'
      : 'hover:bg-slate-800/50' }}">
    <span class="w-5 h-5">
      @include('dashboard.icons.overview')
    </span>
    <span data-sidebar-label class="font-medium">Overview</span>
  </a>

  <a href="/dashboard-reports"
    class="nav-item flex items-center px-4 py-3 rounded-lg
  {{ dashboard_active('dashboard-reports')
      ? 'bg-brand-primary/60 cursor-default'
      : 'hover:bg-slate-800/50' }}">
    <span class="w-5 h-5">
      @include('dashboard.icons.report')
    </span>
    <span data-sidebar-label class="font-medium">Reports</span>
  </a>

  <a href="/dashboard-media"
    class="nav-item flex items-center px-4 py-3 rounded-lg
  {{ dashboard_active('dashboard-media')
      ? 'bg-brand-primary/60 cursor-default'
      : 'hover:bg-slate-800/50' }}">

    <span class="w-5 h-5">
      @include('dashboard.icons.media')
    </span>

    <span data-sidebar-label class="font-medium">Podcasts</span>
  </a>

  <a href="/dashboard-settings"
    class="nav-item flex items-center px-4 py-3 rounded-lg
  {{ dashboard_active('dashboard-settings')
      ? 'bg-brand-primary/60 cursor-default'
      : 'hover:bg-slate-800/50' }}">
    <span class="w-5 h-5">
      @include('dashboard.icons.settings')
    </span>
    <span data-sidebar-label class="font-medium">Settings</span>
  </a>

</nav>






{{-- MOBILE BOTTOM BAR --}}
<div class="md:hidden fixed bottom-0 left-0 right-0 z-40
            bg-brand-primary/95 border-t border-white/10">

  <div class="flex items-center justify-around min-h-[68px]">

    <a href="/dashboard"
      class="flex flex-col items-center justify-center text-white text-xs">
      @include('dashboard.icons.overview')
    </a>

    <a href="/dashboard-reports"
      class="flex flex-col items-center justify-center text-white text-xs">
      @include('dashboard.icons.report')
    </a>

    <a href="/dashboard-media"
      class="flex flex-col items-center justify-center text-white text-xs">
      @include('dashboard.icons.media')
    </a>

    <button type="button"
      data-mobile-menu-toggle
      class="flex flex-col items-center justify-center text-white text-xs">
      @include('dashboard.icons.menu')
    </button>

  </div>
</div>



{{-- MOBILE FLOATING PANEL --}}
<div data-mobile-menu
  class="md:hidden fixed inset-0 z-50 hidden">

  {{-- Overlay --}}
  <div class="absolute inset-0 bg-black/45"
    data-mobile-menu-overlay></div>

  {{-- Floating Card --}}
  <div data-mobile-menu-panel
    class="absolute bottom-20 left-1/2 -translate-x-1/2
              w-[92%] max-w-sm
              rounded-2xl
              bg-white/10
              backdrop-blur-xl
              border border-white/15
              shadow-[0_25px_60px_rgba(0,0,0,0.55)]
              p-5
              space-y-4
              transform translate-y-8 opacity-0
              transition-all duration-300">

    {{-- PROFILE --}}
    <div class="flex items-center gap-3">

      <img src="{{ $avatar_url }}"
        class="w-12 h-12 rounded-full object-cover">

      <div>
        <div class="text-white font-medium">
          {{ $user->display_name }}
        </div>
        <a href="/dashboard-settings"
          class="text-sm text-slate-300">
          View profile
        </a>
      </div>

    </div>

    <div class="border-t border-white/10"></div>

    {{-- LANGUAGE --}}
    @php($langs = pll_the_languages(['raw' => 1]))
    @if(!empty($langs))
    <div class="flex items-center justify-between py-2">

      <div class="flex items-center gap-3 text-white">
        @include('dashboard.icons.language')
        <span>DE/EN</span>
      </div>

      <div class="flex items-center gap-2">
        @foreach($langs as $lang)
        <a href="{{ $lang['url'] }}"
          class="px-3 py-1 rounded-lg text-sm
           {{ $lang['current_lang']
             ? 'bg-white/20 text-white'
             : 'text-slate-300 hover:bg-white/10' }}">
          {{ strtoupper($lang['slug']) }}
        </a>
        @endforeach
      </div>

    </div>
    @endif


    <div class="border-t border-white/10"></div>

    {{-- DARK MODE --}}
    <div class="flex items-center justify-between py-2">

      <div class="flex items-center gap-3 text-white">
        @include('dashboard.icons.theme')
        <span>Dark Mode</span>
      </div>

      <button type="button"
        data-theme-toggle
        data-ajax="{{ admin_url('admin-ajax.php') }}"
        data-nonce="{{ wp_create_nonce('dashboard_theme_toggle') }}"
        class="relative w-11 h-6 bg-white/20 rounded-full transition">

        <span class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition"></span>

      </button>

    </div>


    <div class="border-t border-white/10"></div>

    {{-- SETTINGS --}}
    <a href="/dashboard-settings"
      class="flex items-center justify-between py-2 text-white hover:text-white/80 transition">
      <div class="flex items-center gap-3">
        @include('dashboard.icons.settings')
        <span>Account Settings</span>
      </div>
    </a>

    {{-- LOGOUT (ersetzt About) --}}
    <a href="/?dashboard_logout=1"
      class="flex items-center justify-between py-2 text-red-400 hover:text-red-300 transition">
      <div class="flex items-center gap-3">
        @include('dashboard.icons.logout')
        <span>Logout</span>
      </div>
    </a>


    <div class="border-t border-white/10"></div>

    {{-- CHEVRON CLOSE UNTEN --}}
    <button type="button"
      data-mobile-menu-close
      class="flex items-center justify-center w-full pt-2 text-white/70 hover:text-white transition">
      @include('dashboard.icons.chevron-down')
    </button>

  </div>

</div>





{{-- Logout --}}
{{-- Geändert: px-4 statt px-6 für bündige Icons --}}
<div class="px-4 py-4 border-t border-slate-800 text-sm">
  {{-- Geändert: px-4 hinzugefügt für das Alignment innerhalb des Containers --}}
  <a href="/?dashboard_logout=1"
    class="nav-item flex items-center gap-3 text-slate-400 hover:text-white px-4">
    <span class="w-5 h-5">
      @include('dashboard.icons.logout')
    </span>
    <span data-sidebar-label class="font-medium">Logout</span>
  </a>
</div>

{{-- Profil --}}
{{-- Geändert: px-4 statt px-6 für bündige Icons --}}
<div class=" md:block px-4 py-4 border-t border-slate-800">
  <div data-profile class="flex items-center gap-3">
    {{-- Geändert: px-2 statt p-2 zur feineren Abstimmung des Avatars auf der vertikalen Linie --}}
    <a href="/dashboard-settings"
      class="flex items-center gap-3 flex-1 hover:bg-slate-800/50 rounded-lg px-2 py-2 transition">
      <img
        data-avatar
        data-sidebar-avatar
        src="{{ $avatar_url }}"
        alt="{{ $user->display_name }}">
      <div data-sidebar-label class="leading-tight">
        <div class="text-sm font-medium text-white">
          {{ $user->display_name }}
        </div>
        <div class="text-xs text-slate-400">
          View profile
        </div>
      </div>
    </a>
  </div>
</div>

@endif

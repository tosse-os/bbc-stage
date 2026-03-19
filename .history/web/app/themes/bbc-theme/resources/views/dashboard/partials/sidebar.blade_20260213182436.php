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
    src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}"
    alt="Bloombridge Capital">
</div>

{{-- Language Selector (Sidebar) --}}
@php($langs = pll_the_languages(['raw' => 1]))

@if(!empty($langs))
<div class="mt-6 px-4 mt-4 mx-auto flex items-center justify-center">

  <div class="max-w-[80px] flex rounded-lg bg-white/14 backdrop-blur-sm overflow-hidden text-xs font-semibold tracking-wide">

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

</div>
@endif

{{-- THEME SWITCH --}}
<button
  type="button"
  data-theme-toggle
  data-ajax="{{ admin_url('admin-ajax.php') }}"
  data-nonce="{{ wp_create_nonce('dashboard_theme_toggle') }}"
  class="mt-4 mx-auto flex items-center justify-center
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
  <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
  <line x1="9" y1="3" x2="9" y2="21" />
  <path d="M16 15l-3-3 3-3" />
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

  <a href="/dashboard/reports"
    class="nav-item flex items-center px-4 py-3 rounded-lg
  {{ dashboard_active('reports')
      ? 'bg-brand-primary/60 cursor-default'
      : 'hover:bg-slate-800/50' }}">
    <span class="w-5 h-5">
      @include('dashboard.icons.report')
    </span>
    <span data-sidebar-label class="font-medium">Reports</span>
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
<div class="px-4 py-4 border-t border-slate-800">
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

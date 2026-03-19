@if (is_user_logged_in())
@php
$user = wp_get_current_user();
$avatar = get_avatar_url($user->ID, ['size' => 64]);
@endphp

{{-- Logo --}}
<div
  data-sidebar-logo
  class="px-6 py-6 border-b border-slate-800 flex items-center justify-center">
  <img
    src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}"
    alt="Bloombridge Capital">
</div>

{{-- Collapse Switch --}}
<button
  type="button"
  data-sidebar-toggle
  data-ajax="{{ admin_url('admin-ajax.php') }}"
  data-nonce="{{ wp_create_nonce('dashboard_sidebar_state') }}"
  aria-label="Sidebar ein- oder ausklappen"
  class="mx-auto my-3 flex items-center justify-center
         w-10 h-10 rounded-lg
         text-slate-300 hover:text-white
         hover:bg-slate-800/50 transition group">

  {{-- Das "Sidebar-Layout" Icon --}}
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

    {{-- Der Rahmen des Panels --}}
    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
    {{-- Die vertikale Linie, die die Sidebar symbolisiert --}}
    <line x1="9" y1="3" x2="9" y2="21" />
    {{-- Der Pfeil, der die Aktion anzeigt --}}
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
<div class="px-2 py-2 border-t border-slate-800 text-sm">
  <a href="/?dashboard_logout=1"
    class="flex items-center justify-center lg:justify-start px-3 py-3 rounded-lg text-slate-400 hover:text-white transition group">

    {{-- Icon-Container: Fest definiert für perfekte Zentrierung --}}
    <span class="w-6 h-6 flex-shrink-0 flex items-center justify-center">
      @include('dashboard.icons.logout')
    </span>

    {{-- Label: Wird nur angezeigt/hat nur Platz, wenn Sidebar offen ist --}}
    <span data-sidebar-label class="font-medium ml-3 whitespace-nowrap">
      Logout
    </span>
  </a>
</div>

{{-- Profil --}}
<div class="px-6 py-4 border-t border-slate-800">
  <div data-profile class="flex items-center gap-3">

    <a href="/dashboard-settings"
      class="flex items-center gap-3 flex-1 hover:bg-slate-800/50 rounded-lg p-2 transition">
      @php
      $avatar_id = get_user_meta($user->ID, 'dashboard_avatar_id', true);
      $avatar_url = $avatar_id
      ? wp_get_attachment_image_url($avatar_id, 'thumbnail')
      : get_avatar_url($user->ID);
      @endphp

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

    {{--
    <button
      type="button"
      data-profile-menu-trigger
      class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition"
      aria-label="Profile options">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
        class="w-5 h-5" fill="currentColor">
        <circle cx="12" cy="5" r="1.8" />
        <circle cx="12" cy="12" r="1.8" />
        <circle cx="12" cy="19" r="1.8" />
      </svg>
    </button>
    --}}

  </div>
</div>

@endif

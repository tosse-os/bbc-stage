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
  aria-label="Sidebar ein- oder ausklappen"
  class="mx-auto my-3 flex items-center justify-center
         w-10 h-10 rounded-lg
         text-slate-300 hover:text-white
         hover:bg-slate-800/50 transition">
  <svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    width="24"
    height="24"
    fill="none"
    stroke="currentColor"
    stroke-width="2.5"
    stroke-linecap="round"
    stroke-linejoin="round">
    <line x1="3" y1="6" x2="21" y2="6" />
    <line x1="3" y1="12" x2="21" y2="12" />
    <line x1="3" y1="18" x2="21" y2="18" />
  </svg>

</button>

{{-- Navigation --}}
<nav class="flex-1 px-4 py-6 space-y-1 text-sm">

  <a href="/dashboard"
    class="nav-item flex items-center px-4 py-3 rounded-lg hover:bg-slate-800/50">
    <span class="w-5 h-5">
      @include('dashboard.icons.overview')
    </span>
    <span data-sidebar-label class="font-medium">Overview</span>
  </a>

  <a href="/dashboard/reports"
    class="nav-item flex items-center px-4 py-3 rounded-lg hover:bg-slate-800/50">
    <span class="w-5 h-5">
      @include('dashboard.icons.report')
    </span>
    <span data-sidebar-label class="font-medium">Reports</span>
  </a>

  <a href="/dashboard/settings"
    class="nav-item flex items-center px-4 py-3 rounded-lg hover:bg-slate-800/50">
    <span class="w-5 h-5">
      @include('dashboard.icons.settings')
    </span>
    <span data-sidebar-label class="font-medium">Settings</span>
  </a>

</nav>

{{-- Logout --}}
<div class="px-6 py-4 border-t border-slate-800 text-sm">
  <a href="/?dashboard_logout=1"
    class="nav-item flex items-center gap-3 text-slate-400 hover:text-white">
    <span class="w-5 h-5">
      @include('dashboard.icons.logout')
    </span>
    <span data-sidebar-label class="font-medium">Logout</span>
  </a>
</div>

{{-- Profil --}}
<div class="px-6 py-4 border-t border-slate-800">
  <div data-profile class="flex items-center gap-3">

    <a href="/dashboard/profile"
      class="flex items-center gap-3 flex-1 hover:bg-slate-800/50 rounded-lg p-2 transition">
      <img
        data-avatar
        src="{{ $avatar }}"
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

  </div>
</div>

@endif

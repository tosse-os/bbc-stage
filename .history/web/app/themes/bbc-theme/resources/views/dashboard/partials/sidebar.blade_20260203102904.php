@if (is_user_logged_in())
@php
$user = wp_get_current_user();
$avatar = get_avatar_url($user->ID, ['size' => 64]);
@endphp

<aside
  data-sidebar
  data-collapsed="0"
  class="w-72 transition-all duration-200 text-slate-100 flex flex-col">

  {{-- Logo --}}
  <div class="px-6 py-6 border-b border-slate-800 flex items-center justify-between">
    <img
      data-sidebar-logo
      src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}"
      alt="Bloombridge Capital"
      class="h-18 w-auto transition-all duration-200">

    <button
      type="button"
      data-sidebar-toggle
      class="p-2 text-slate-400 hover:text-white"
      aria-label="Sidebar umschalten">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor">
        <rect x="4" y="11" width="16" height="2"></rect>
      </svg>
    </button>
  </div>

  {{-- Navigation --}}
  <nav class="flex-1 px-4 py-6 space-y-1 text-sm">

    <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-slate-800/60">
      <span class="w-5 h-5">@include('dashboard.icons.overview')</span>
      <span data-sidebar-label class="font-medium">Overview</span>
    </a>

    <a href="/dashboard/reports" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800/50">
      <span class="w-5 h-5">@include('dashboard.icons.report')</span>
      <span data-sidebar-label class="font-medium">Reports</span>
    </a>

    <a href="/dashboard/settings" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800/50">
      <span class="w-5 h-5">@include('dashboard.icons.settings')</span>
      <span data-sidebar-label class="font-medium">Settings</span>
    </a>

  </nav>

  {{-- Logout --}}
  <div class="px-6 py-4 border-t border-slate-800 text-sm">
    <a href="/?dashboard_logout=1" class="flex items-center gap-3 text-slate-400 hover:text-white">
      <span class="w-5 h-5">@include('dashboard.icons.logout')</span>
      <span data-sidebar-label class="font-medium">Logout</span>
    </a>
  </div>

  {{-- Profil --}}
  <div class="px-6 py-4 border-t border-slate-800">
    <div class="flex items-center gap-3">

      <a href="/dashboard/profile" class="flex items-center gap-3 flex-1 hover:bg-slate-800/50 rounded-lg p-2 transition">
        <img src="{{ $avatar }}" class="w-9 h-9 rounded-full object-cover">
        <div data-sidebar-label class="leading-tight">
          <div class="text-sm font-medium text-white">{{ $user->display_name }}</div>
          <div class="text-xs text-slate-400">View profile</div>
        </div>
      </a>

      <button
        type="button"
        data-profile-menu-trigger
        class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition"
        aria-label="Profile options">

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-5 h-5" fill="currentColor">
          <circle cx="12" cy="5" r="1.8"></circle>
          <circle cx="12" cy="12" r="1.8"></circle>
          <circle cx="12" cy="19" r="1.8"></circle>
        </svg>

      </button>

    </div>
  </div>

</aside>
@endif

@if (is_user_logged_in())
@php
$user = wp_get_current_user();
$avatar = get_avatar_url($user->ID, ['size' => 64]);
@endphp

<aside class="w-72 flex flex-col text-slate-100 bg-slate-950/80 backdrop-blur">

  {{-- Logo --}}
  <div class="px-6 py-6 border-b border-slate-800 flex items-center justify-center">
    <img
      src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}"
      alt="Bloombridge Capital"
      class="h-18 w-auto">
  </div>

  {{-- Navigation --}}
  <nav class="flex-1 px-4 py-6 space-y-1 text-sm">

    <a href="/dashboard"
      class="flex items-center gap-3 px-4 py-3 rounded-lg bg-slate-800/60">
      <span class="w-5 h-5">
        @include('dashboard.icons.overview')
      </span>
      <span class="font-medium">Overview</span>
    </a>

    <a href="/dashboard/reports"
      class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800/50">
      <span class="w-5 h-5">
        @include('dashboard.icons.report')
      </span>
      <span class="font-medium">Reports</span>
    </a>

    <a href="/dashboard/settings"
      class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800/50">
      <span class="w-5 h-5">
        @include('dashboard.icons.settings')
      </span>
      <span class="font-medium">Settings</span>
    </a>

  </nav>

  {{-- User Profile --}}
  <div class="px-4 py-4 border-t border-slate-800">

    <a href="/dashboard/profile"
      class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-800/60 transition">

      <img
        src="{{ $avatar }}"
        alt="{{ $user->display_name }}"
        class="w-9 h-9 rounded-full object-cover">

      <div class="flex-1 leading-tight">
        <div class="text-sm font-medium text-white">
          {{ $user->display_name }}
        </div>
        <div class="text-xs text-slate-400">
          View profile
        </div>
      </div>

      <span class="text-slate-400">
        ⋮
      </span>

    </a>

  </div>

  {{-- Logout --}}
  <div class="px-4 pb-4">
    <a href="/?dashboard_logout=1"
      class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition text-sm">
      <span class="w-5 h-5">
        @include('dashboard.icons.logout')
      </span>
      <span class="font-medium">Logout</span>
    </a>
  </div>

</aside>
@endif

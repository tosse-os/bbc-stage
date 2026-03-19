@if (is_user_logged_in())
@php
$user = wp_get_current_user();
$avatar = get_avatar_url($user->ID, ['size' => 64]);
@endphp

<aside
  data-sidebar
  data-collapsed="0"
  class="w-72 flex flex-col transition-all duration-200">

  {{-- Header --}}
  <div
    data-sidebar-header
    class="flex items-center justify-between px-6 py-6 transition-all duration-200">

    <img
      data-sidebar-logo
      src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}"
      class="h-10 w-auto transition-all duration-200">

    <button
      type="button"
      data-sidebar-toggle
      class="p-2">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-4 h-4" fill="currentColor">
        <rect x="4" y="11" width="16" height="2"></rect>
      </svg>
    </button>
  </div>

  {{-- Navigation --}}
  <nav
    data-sidebar-nav
    class="flex-1 px-4 py-6 space-y-1 transition-all duration-200 text-sm">

    <a href="/dashboard" class="flex items-center gap-3 px-4 py-3">
      <span class="w-5 h-5">@include('dashboard.icons.overview')</span>
      <span data-sidebar-label>Overview</span>
    </a>

    <a href="/dashboard/reports" class="flex items-center gap-3 px-4 py-3">
      <span class="w-5 h-5">@include('dashboard.icons.report')</span>
      <span data-sidebar-label>Reports</span>
    </a>

    <a href="/dashboard/settings" class="flex items-center gap-3 px-4 py-3">
      <span class="w-5 h-5">@include('dashboard.icons.settings')</span>
      <span data-sidebar-label>Settings</span>
    </a>

  </nav>

  {{-- Footer --}}
  <div
    data-sidebar-footer
    class="px-6 py-4 transition-all duration-200">

    <div class="flex items-center gap-3">
      <img src="{{ $avatar }}" class="w-9 h-9 rounded-full object-cover">
      <div data-sidebar-label>
        <div class="text-sm">{{ $user->display_name }}</div>
      </div>
    </div>

  </div>

</aside>
@endif

@if (is_user_logged_in())
@php
$user = wp_get_current_user();
$avatar = get_avatar_url($user->ID, ['size' => 64]);
@endphp

{{-- Die Sidebar erhält das data-sidebar Attribut und eine Transition-Klasse --}}
<aside data-sidebar
  class="w-72 bg-slate-900 text-slate-100 flex flex-col transition-all duration-300 ease-in-out border-r border-slate-800 h-screen sticky top-0">

  {{-- Logo & Toggle Bereich --}}
  <div class="px-4 py-6 border-b border-slate-800 flex items-center justify-between overflow-hidden">
    <div data-sidebar-logo class="flex-shrink-0 transition-all duration-300">
      <img src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}"
        alt="Bloombridge Capital"
        class="h-12 w-auto object-contain">
    </div>

    {{-- Der Switch zum Kleiner-Machen --}}
    <button type="button"
      data-sidebar-toggle
      class="p-2 rounded-lg hover:bg-slate-800 text-slate-400 hover:text-white transition-colors">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
      </svg>
    </button>
  </div>

  {{-- Navigation --}}
  <nav class="flex-1 px-3 py-6 space-y-2 text-sm overflow-x-hidden">
    @php
    $navItems = [
    ['url' => '/dashboard', 'icon' => 'overview', 'label' => 'Overview'],
    ['url' => '/dashboard/reports', 'icon' => 'report', 'label' => 'Reports'],
    ['url' => '/dashboard/settings', 'icon' => 'settings', 'label' => 'Settings'],
    ];
    @endphp

    @foreach($navItems as $item)
    <a href="{{ $item['url'] }}"
      class="flex items-center gap-3 px-3 py-3 rounded-lg hover:bg-slate-800/50 transition-all group {{ request()->is(trim($item['url'], '/')) ? 'bg-slate-800/60' : '' }}">
      <span class="w-6 h-6 flex-shrink-0">
        @include('dashboard.icons.' . $item['icon'])
      </span>
      <span data-sidebar-label class="font-medium whitespace-nowrap transition-opacity duration-300">
        {{ $item['label'] }}
      </span>
    </a>
    @endforeach
  </nav>

  {{-- Logout --}}
  <div class="px-3 py-4 border-t border-slate-800">
    <a href="/?dashboard_logout=1" class="flex items-center gap-3 px-3 py-2 text-slate-400 hover:text-white group">
      <span class="w-6 h-6 flex-shrink-0">
        @include('dashboard.icons.logout')
      </span>
      <span data-sidebar-label class="font-medium whitespace-nowrap">Logout</span>
    </a>
  </div>

  {{-- Profil Bereich --}}
  <div class="px-3 py-4 border-t border-slate-800">
    <div class="flex items-center gap-3">
      <a href="/dashboard/profile" class="flex items-center gap-3 flex-1 hover:bg-slate-800/50 rounded-lg p-2 transition overflow-hidden">
        <img src="{{ $avatar }}" alt="{{ $user->display_name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0">
        <div data-sidebar-label class="leading-tight transition-opacity duration-300">
          <div class="text-sm font-medium text-white truncate">{{ $user->display_name }}</div>
          <div class="text-xs text-slate-400">View profile</div>
        </div>
      </a>
    </div>
  </div>

</aside>
@endif

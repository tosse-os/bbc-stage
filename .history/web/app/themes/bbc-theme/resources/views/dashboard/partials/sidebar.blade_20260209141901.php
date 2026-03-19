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
{{-- Container-Padding auf px-4 angepasst, um bündig mit <nav> zu sein --}}
<div class="px-4 py-2 border-t border-slate-800 text-sm">
  <a href="/?dashboard_logout=1"
    {{-- px-4 py-3 identisch zu den Nav-Items oben --}}
    class="nav-item flex items-center px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition group">
    <span class="w-5 h-5 flex items-center justify-center flex-shrink-0">
      @include('dashboard.icons.logout')
    </span>
    {{-- ml-3 sorgt für den exakten Abstand wie oben --}}
    <span data-sidebar-label class="font-medium ml-3">Logout</span>
  </a>
</div>

{{-- Profil --}}
{{-- Ebenfalls px-4 für die vertikale Linie --}}
<div class="px-4 py-4 border-t border-slate-800">
  <div data-profile>
    {{-- p-2 am Link leicht reduziert, damit das Avatar-Icon (das meist etwas breiter wirkt) optisch zentriert bleibt --}}
    <a href="/dashboard-settings"
      class="flex items-center px-2 py-2 rounded-lg hover:bg-slate-800/50 transition group">
      @php
      $avatar_id = get_user_meta($user->ID, 'profile_avatar_id', true);
      $avatar_url = $avatar_id
      ? wp_get_attachment_image_url($avatar_id, 'thumbnail')
      : get_avatar_url($user->ID);
      @endphp

      {{-- Avatar-Container: w-8 h-8 ist der Standard für diese Sidebar-Größe --}}
      <div class="w-8 h-8 flex-shrink-0">
        <img
          data-avatar
          data-sidebar-avatar
          src="{{ $avatar_url }}"
          alt="{{ $user->display_name }}"
          class="w-full h-full rounded-full object-cover border border-slate-700">
      </div>

      <div data-sidebar-label class="leading-tight ml-3">
        <div class="text-sm font-medium text-white group-hover:text-brand-primary transition-colors">
          {{ $user->display_name }}
        </div>
        <div class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mt-0.5">
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

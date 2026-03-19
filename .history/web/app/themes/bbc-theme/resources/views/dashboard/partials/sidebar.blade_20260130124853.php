@if (is_user_logged_in())
<aside class="w-72 text-slate-100 flex flex-col">

  <div class="px-6 py-6 border-b border-slate-800 flex items-center justify-center">
    <img
      src="{{ Vite::asset('resources/images/dashboard/bloombridge-capital-logo-v2.png') }}"
      alt="Bloombridge Capital"
      class="h-17 w-auto">
  </div>

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

  <div class="px-6 py-4 border-t border-slate-800 text-sm">
    <a href="{{ wp_logout_url('/dashboard-login') }}"
      class="flex items-center gap-3 text-slate-400 hover:text-white">
      <span class="w-5 h-5">
        @include('dashboard.icons.logout')
      </span>

      <span class="font-medium">Logout</span>
    </a>
  </div>

</aside>
@endif

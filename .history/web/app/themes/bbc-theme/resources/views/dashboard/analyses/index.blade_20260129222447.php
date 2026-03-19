@if (is_user_logged_in())
<aside class="w-72 bg-slate-900 text-slate-100 flex flex-col">

  <div class="px-6 py-6 border-b border-slate-800">
    <span class="text-lg font-semibold tracking-wide">
      Dashboard
    </span>
  </div>

  <nav class="flex-1 px-4 py-6 space-y-1 text-sm">
    <a href="/dashboard"
      class="block px-4 py-2 rounded-lg bg-slate-800">
      Übersicht
    </a>

    <a href="/dashboard/settings/billing"
      class="block px-4 py-2 rounded-lg hover:bg-slate-800">
      Abrechnung
    </a>
  </nav>

  <div class="px-6 py-4 border-t border-slate-800 text-sm text-slate-400">
    <a href="{{ wp_logout_url('/dashboard-login') }}"
      class="hover:text-white">
      Logout
    </a>
  </div>

</aside>
@endif

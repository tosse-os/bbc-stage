@if (is_user_logged_in())
<aside class="w-72 bg-dashboard-card border-r border-dashboard-border flex flex-col">

  <div class="px-6 py-6 border-b border-dashboard-border">
    <span class="text-lg font-semibold tracking-wide">
      Dashboard
    </span>
  </div>

  <nav class="flex-1 px-6 py-6 space-y-2 text-sm">
    <a href="/dashboard" class="block px-3 py-2 rounded-lg hover:bg-dashboard-hover">
      Übersicht
    </a>

    <a href="/dashboard/settings/billing" class="block px-3 py-2 rounded-lg hover:bg-dashboard-hover">
      Abrechnung
    </a>
  </nav>

  <div class="px-6 py-4 border-t border-dashboard-border text-sm">
    <a href="{{ wp_logout_url('/dashboard-login') }}" class="block text-dashboard-muted hover:text-dashboard-text">
      Logout
    </a>
  </div>

</aside>
@endif

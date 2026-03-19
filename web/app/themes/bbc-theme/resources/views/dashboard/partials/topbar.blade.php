{{-- resources/views/dashboard/partials/topbar.blade.php --}}
@if (is_user_logged_in())
<header class="h-14 flex items-center justify-between px-6 border-b border-dashboard-border">
  <nav class="flex gap-4 text-sm">
    <a href="/dashboard">Übersicht</a>
    <a href="/dashboard/settings/billing">Abrechnung</a>
    <a href="{{ wp_logout_url('/dashboard-login') }}">Logout</a>
  </nav>
  <button data-toggle-theme>Theme wechseln</button>
</header>
@endif

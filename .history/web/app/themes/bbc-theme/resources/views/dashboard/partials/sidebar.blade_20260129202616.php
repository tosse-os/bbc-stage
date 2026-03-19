{{-- resources/views/dashboard/partials/sidebar.blade.php --}}
@if (is_user_logged_in())
<aside class="w-64 bg-dashboard-card border-r border-dashboard-border p-6">
  <nav class="space-y-4">
    <a href="/dashboard" class="block">Übersicht</a>
    <a href="/dashboard/settings/billing" class="block">Abrechnung</a>
    <a href="{{ wp_logout_url('/dashboard-login') }}" class="block">Logout</a>
  </nav>
</aside>
@endif

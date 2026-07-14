{{-- resources/views/dashboard/partials/topbar.blade.php --}}
@if (is_user_logged_in())
<header class="h-14 flex items-center justify-between px-6 border-b border-dashboard-border">
  <nav class="flex gap-4 text-sm">
    <a href="{{ dashboard_url('dashboard') }}">{{ dashboard_t('nav.overview') }}</a>
    <a href="{{ dashboard_settings_billing_url() }}">{{ dashboard_t('settings.tabs.billing') }}</a>
    <a href="{{ dashboard_logout_url() }}">{{ dashboard_t('nav.logout') }}</a>
  </nav>
  <button data-toggle-theme>{{ dashboard_t('nav.light_mode') }}</button>
</header>
@endif

<?php


/**
 * Dashboard-Gate
 * Erzwingt Zugriffskontrolle & Redirects
 */
add_action('template_redirect', function () {

  $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

  if (!str_starts_with($uri, '/dashboard')) {
    return;
  }

  /**
   * Eingeloggte User dürfen Login & Register NICHT sehen
   */
  if (
    is_user_logged_in() &&
    ($uri === '/dashboard-login' || $uri === '/dashboard-register')
  ) {
    wp_redirect('/dashboard');
    exit;
  }

  /**
   * Login & Register für Gäste erlauben
   */
  if ($uri === '/dashboard-login' || $uri === '/dashboard-register') {
    return;
  }

  /**
   * Gäste dürfen nicht ins Dashboard
   */
  if (!is_user_logged_in()) {
    wp_redirect('/dashboard-login');
    exit;
  }

  /**
   * Trial abgelaufen → nur Billing erlauben
   */
  if (
    dashboard_access_state(get_current_user_id()) === 'payment_required' &&
    $uri !== '/dashboard/settings/billing'
  ) {
    wp_redirect('/dashboard');
    exit;
  }
});

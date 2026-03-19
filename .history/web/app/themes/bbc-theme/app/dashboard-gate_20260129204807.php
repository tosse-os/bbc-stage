<?php

/**
 * Erzwingt Dashboard-Zugriffsregeln auf URL-Ebene
 * - Gäste → Login
 * - Abgelaufener Trial → Payment-Seite
 */

<?php

add_action('template_redirect', function () {
  $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

  if (!str_starts_with($uri, '/dashboard')) {
    return;
  }

  if ($uri === '/dashboard-login' || $uri === '/dashboard-register') {
    return;
  }

  if (!is_user_logged_in()) {
    wp_redirect('/dashboard-login');
    exit;
  }

  if (dashboard_access_state(get_current_user_id()) === 'payment_required' && $uri !== '/dashboard/settings/billing') {
    wp_redirect('/dashboard');
    exit;
  }
});

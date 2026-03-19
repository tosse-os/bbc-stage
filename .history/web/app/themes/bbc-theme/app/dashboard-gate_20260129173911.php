<?php

/**
 * Erzwingt Dashboard-Zugriffsregeln auf URL-Ebene
 * - Gäste → Login
 * - Abgelaufener Trial → Payment-Seite
 */
add_action('template_redirect', function () {
  if (!str_starts_with($_SERVER['REQUEST_URI'], '/dashboard')) {
    return;
  }

  if (!is_user_logged_in()) {
    wp_redirect('/dashboard/login');
    exit;
  }

  $state = dashboard_access_state(get_current_user_id());

  if ($state === 'payment_required' && !str_starts_with($_SERVER['REQUEST_URI'], '/dashboard/settings')) {
    wp_redirect('/dashboard');
    exit;
  }
});

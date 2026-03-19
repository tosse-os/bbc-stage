<?php

/**
 * Dashboard Route-Gate
 * Erzwingt Login, Trial- und Payment-Regeln auf URL-Ebene.
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

  if ($state === 'payment_required') {
    if (!str_starts_with($_SERVER['REQUEST_URI'], '/dashboard')) {
      wp_redirect('/dashboard');
      exit;
    }
  }
});

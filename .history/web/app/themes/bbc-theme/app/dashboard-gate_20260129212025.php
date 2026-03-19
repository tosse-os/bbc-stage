<?php

/**
 * Dashboard-Gate
 *
 * Zugriff wird NICHT über URL-Prefix geregelt,
 * sondern ausschließlich über das verwendete Page-Template.
 */

add_action('template_redirect', function () {

  if (!is_page()) {
    return;
  }

  $template = get_page_template_slug();

  // Login & Register sind IMMER erlaubt
  if (in_array($template, [
    'page-dashboard-login.blade.php',
    'page-dashboard-register.blade.php',
  ], true)) {
    return;
  }

  // Nur Dashboard-Seiten schützen
  if ($template !== 'page-dashboard.blade.php') {
    return;
  }

  if (!is_user_logged_in()) {
    wp_redirect('/dashboard-login');
    exit;
  }

  if (dashboard_access_state(get_current_user_id()) === 'payment_required') {
    wp_redirect('/dashboard');
    exit;
  }
});

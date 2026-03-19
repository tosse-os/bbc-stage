<?php

/**
 * Dashboard-Gate
 *
 * Zugriffskontrolle ausschließlich über Page-Templates
 */

add_action('template_redirect', function () {

  if (!is_page()) {
    return;
  }

  $template = get_page_template_slug();

  // Wenn eingeloggt → Login & Register verbieten
  if (is_user_logged_in() && in_array($template, [
    'page-dashboard-login.blade.php',
    'page-dashboard-register.blade.php',
  ], true)) {
    wp_redirect('/dashboard');
    exit;
  }

  // Login & Register für Gäste erlauben
  if (in_array($template, [
    'page-dashboard-login.blade.php',
    'page-dashboard-register.blade.php',
  ], true)) {
    return;
  }

  // Nur Dashboard schützen
  if ($template !== 'page-dashboard.blade.php') {
    return;
  }

  // Gast → Login
  if (!is_user_logged_in()) {
    wp_redirect('/dashboard-login');
    exit;
  }
});

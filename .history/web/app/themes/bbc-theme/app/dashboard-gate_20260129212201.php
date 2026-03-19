<?php

/**
 * Dashboard-Gate
 *
 * Erzwingt Zugriffskontrolle ausschließlich über Page-Templates.
 * KEIN URL-Prefix-Gate mehr.
 *
 * Regeln:
 * - Login & Register sind immer erlaubt
 * - Dashboard-Seite erfordert Login
 * - Trial abgelaufen → Payment-Zustand
 */

add_action('template_redirect', function () {

  if (!is_page()) {
    return;
  }

  $template = get_page_template_slug();

  // Login & Registrierung immer erlauben
  if (in_array($template, [
    'page-dashboard-login.blade.php',
    'page-dashboard-register.blade.php',
  ], true)) {
    return;
  }

  // Nur Dashboard-Seite schützen
  if ($template !== 'page-dashboard.blade.php') {
    return;
  }

  // Nicht eingeloggt → Login
  if (!is_user_logged_in()) {
    wp_redirect('/dashboard-login');
    exit;
  }

  // Trial abgelaufen → Payment-Zustand
  if (dashboard_access_state(get_current_user_id()) === 'payment_required') {
    wp_redirect('/dashboard');
    exit;
  }
});

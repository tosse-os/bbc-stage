<?php

/**
 * Erzwingt Dashboard-Zugriffsregeln auf URL-Ebene
 * - Gäste → Login
 * - Abgelaufener Trial → Payment-Seite
 */

add_action('template_redirect', function () {
  $uri = $_SERVER['REQUEST_URI'];

  if (!str_starts_with($uri, '/dashboard')) {
    return;
  }

  if (
    str_starts_with($uri, '/dashboard-login') ||
    str_starts_with($uri, '/dashboard-register')
  ) {
    return;
  }

  // Gäste auf Login schicken
  if (!is_user_logged_in() && $uri === '/dashboard') {
    wp_redirect('/dashboard-login');
    exit;
  }

  // Eingeloggte dürfen NICHT auf Login bleiben
  if (is_user_logged_in() && $uri === '/dashboard-login') {
    wp_redirect('/dashboard');
    exit;
  }
});

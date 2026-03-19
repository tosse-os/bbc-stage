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
    str_starts_with($uri, '/dashboard/login') ||
    str_starts_with($uri, '/dashboard/register')
  ) {
    return;
  }

  if (!is_user_logged_in()) {
    wp_redirect('/dashboard/login');
    exit;
  }
});

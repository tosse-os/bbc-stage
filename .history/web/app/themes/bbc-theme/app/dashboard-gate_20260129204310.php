<?php

/**
 * Erzwingt Dashboard-Zugriffsregeln auf URL-Ebene
 * - Gäste → Login
 * - Abgelaufener Trial → Payment-Seite
 */

add_action('template_redirect', function () {
  $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

  // Nicht Dashboard → raus
  if (!str_starts_with($uri, '/dashboard')) {
    return;
  }

  // 🔥 Eingeloggt → Login-Seite ist verboten
  if (is_user_logged_in() && $uri === '/dashboard-login') {
    wp_redirect('/dashboard');
    exit;
  }

  // Login / Register dürfen für Gäste erreichbar sein
  if (
    $uri === '/dashboard-login' ||
    $uri === '/dashboard-register'
  ) {
    return;
  }

  // Gast → Dashboard verboten
  if (!is_user_logged_in() && $uri === '/dashboard') {
    wp_redirect('/dashboard-login');
    exit;
  }
});

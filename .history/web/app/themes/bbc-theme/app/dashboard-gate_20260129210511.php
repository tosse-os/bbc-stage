<?php

/**
 * Dashboard-Gate
 *
 * Erzwingt die Zugriffskontrolle auf URL-Ebene.
 * Läuft bei jedem Frontend-Request über template_redirect.
 *
 * Regeln:
 * - Alles außerhalb von /dashboard* wird ignoriert
 * - /dashboard-login und /dashboard-register sind IMMER erlaubt
 * - Alle anderen /dashboard*-URLs erfordern Login
 * - Abgelaufener Trial → Redirect auf /dashboard (Payment-View)
 */

add_action('template_redirect', function () {

  // Aktuelle URL ohne Query-Parameter und ohne abschließenden Slash
  $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

  // Nicht Teil des Dashboards → Gate greift nicht
  if (!str_starts_with($uri, '/dashboard')) {
    return;
  }

  // Login- und Registrierungsseiten sind immer erlaubt
  if ($uri === '/dashboard-login' || $uri === '/dashboard-register') {
    return;
  }

  // Nicht eingeloggt → immer zum Login weiterleiten
  if (!is_user_logged_in()) {
    wp_redirect('/dashboard-login');
    exit;
  }

  // Eingeloggt, aber Trial abgelaufen → nur Billing-Seite erlauben
  if (
    dashboard_access_state(get_current_user_id()) === 'payment_required' &&
    $uri !== '/dashboard/settings/billing'
  ) {
    wp_redirect('/dashboard');
    exit;
  }
});

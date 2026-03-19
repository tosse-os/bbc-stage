<?php

/**
 * Dashboard Startlogik
 * Entscheidet, welche View auf /dashboard gerendert wird.
 */

function dashboard_start_view()
{
  $state = dashboard_access_state(get_current_user_id());

  if ($state === 'payment_required') {
    return 'dashboard.partials.payment-required';
  }

  $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

  if (str_starts_with($path, 'dashboard-media')) {
    return 'dashboard.media.index';
  }


  return 'dashboard.analyses.index';
}

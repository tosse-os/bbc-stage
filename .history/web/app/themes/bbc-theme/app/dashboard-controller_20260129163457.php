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

  return 'dashboard.analyses.index';
}

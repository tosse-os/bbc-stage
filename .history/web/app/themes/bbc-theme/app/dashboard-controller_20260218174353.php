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

  global $post;

  if (!$post) {
    return 'dashboard.overview';
  }

  $slug = $post->post_name;

  if ($slug === 'dashboard-reports') {
    return 'dashboard.analyses.index';
  }

  if ($slug === 'dashboard-media') {
    return 'dashboard.media-entry.index';
  }

  return 'dashboard.overview';
}

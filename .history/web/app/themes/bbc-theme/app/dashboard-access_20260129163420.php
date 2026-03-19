<?php

/**
 * Zentrale Zugriffsbewertung für das Dashboard
 * Liefert den aktuellen Zugriffsstatus des Users.
 */

function dashboard_access_state($user_id = null)
{
  if (!$user_id) {
    return 'guest';
  }

  $status = get_user_meta($user_id, USER_META_SUB_STATUS, true);
  $trial_end = (int) get_user_meta($user_id, USER_META_TRIAL_END, true);
  $now = current_time('timestamp');

  if ($status === 'active') {
    return 'active';
  }

  if ($status === 'trial' && $trial_end >= $now) {
    return 'trial';
  }

  return 'payment_required';
}

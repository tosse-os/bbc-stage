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

  $status = (string) get_user_meta($user_id, USER_META_SUB_STATUS, true);
  $trial_end = (int) get_user_meta($user_id, USER_META_TRIAL_END, true);
  $now = current_time('timestamp');

  if ($status === 'active') {
    return 'active';
  }

  if ($status === 'trial' && $trial_end >= $now) {
    return 'trial';
  }

  if (in_array($status, ['past_due', 'canceled'], true)) {
    return $status;
  }

  return 'payment_required';
}

function dashboard_user_has_premium_access($user_id = null): bool
{
  return in_array(dashboard_access_state($user_id), ['trial', 'active'], true);
}

function dashboard_user_is_locked($user_id = null): bool
{
  return !dashboard_user_has_premium_access($user_id);
}

function dashboard_billing_url(): string
{
  return dashboard_settings_billing_url();
}

function dashboard_payment_required_url(): string
{
  return dashboard_url('dashboard-payment-required');
}

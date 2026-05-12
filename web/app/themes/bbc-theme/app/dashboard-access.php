<?php

function dashboard_request_allows_stripe_sync_skip(): bool
{
  if (empty($_GET['nosync'])) {
    return false;
  }

  if ((string) $_GET['nosync'] !== '1') {
    return false;
  }

  return is_user_logged_in() && current_user_can('manage_options');
}

function dashboard_maybe_sync_user_subscription_before_access($user_id = null, bool $force = false): void
{
  $user_id = $user_id ? (int) $user_id : 0;

  if ($user_id <= 0) {
    return;
  }

  if (!$force && dashboard_request_allows_stripe_sync_skip()) {
    return;
  }

  if (!function_exists('dashboard_stripe_sync_user_subscription_from_stripe')) {
    return;
  }

  static $running = false;
  static $synced = [];

  if ($running) {
    return;
  }

  if (!$force && isset($synced[$user_id])) {
    return;
  }

  $running = true;

  try {
    dashboard_stripe_sync_user_subscription_from_stripe($user_id, $force);
  } finally {
    $running = false;
    $synced[$user_id] = true;
  }
}

/**
 * Zentrale Zugriffsbewertung für das Dashboard
 * Liefert den aktuellen Zugriffsstatus des Users.
 */

function dashboard_access_state($user_id = null)
{
  if (!$user_id) {
    return 'guest';
  }

  dashboard_maybe_sync_user_subscription_before_access((int) $user_id);

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
  return home_url('/dashboard-settings') . '?tab=billing';
}

function dashboard_payment_required_url(): string
{
  return home_url('/dashboard-payment-required');
}

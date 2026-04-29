<?php

function dashboard_handle_dashboard_gate()
{
  if (!is_page()) {
    return;
  }

  $post = get_queried_object();

  if (!$post) {
    return;
  }

  $slug = $post->post_name;

  $isDashboardArea =
    $slug === 'dashboard' ||
    str_starts_with($slug, 'dashboard-');

  if (!$isDashboardArea) {
    return;
  }

  $isLoginPage =
    $slug === 'dashboard-login' ||
    $slug === 'dashboard-register' ||
    $slug === 'dashboard-password';

  $isBillingPage =
    $slug === 'dashboard-settings' &&
    (($_GET['tab'] ?? 'account') === 'billing');

  $isPaymentRequiredPage =
    $slug === 'dashboard-payment-required';

  if (is_user_logged_in()) {
    $user = wp_get_current_user();

    if (function_exists('dashboard_user_should_use_wp_admin') && dashboard_user_should_use_wp_admin($user)) {
      wp_safe_redirect(admin_url());
      exit;
    }
  }

  if (!is_user_logged_in() && !$isLoginPage) {
    wp_safe_redirect('/dashboard-login');
    exit;
  }

  if (is_user_logged_in() && $isLoginPage) {
    wp_safe_redirect('/dashboard');
    exit;
  }

  $state = dashboard_access_state(get_current_user_id());

  if (
    $state === 'payment_required' &&
    !$isLoginPage &&
    !$isBillingPage &&
    !$isPaymentRequiredPage
  ) {
    wp_safe_redirect('/dashboard-payment-required');
    exit;
  }
}

add_action('template_redirect', 'dashboard_handle_dashboard_gate', 99);

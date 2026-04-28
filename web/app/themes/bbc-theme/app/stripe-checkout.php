<?php

/**
 * Startet den Stripe-Checkout für eine Subscription.
 */
function dashboard_handle_start_checkout()
{
  if (!is_user_logged_in()) {
    wp_safe_redirect('/dashboard-login');
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_start_checkout')
  ) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=invalid_request');
    exit;
  }

  if (!dashboard_stripe_boot()) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=stripe_sdk_missing');
    exit;
  }

  $configError = dashboard_stripe_checkout_config_error();

  if ($configError !== '') {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=' . rawurlencode($configError));
    exit;
  }

  $user = wp_get_current_user();

  try {
    $customer = dashboard_get_or_create_stripe_customer($user);

    $session = \Stripe\Checkout\Session::create([
      'mode' => 'subscription',
      'customer' => $customer->id,
      'line_items' => [[
        'price' => dashboard_stripe_price_id(),
        'quantity' => 1,
      ]],
      'success_url' => dashboard_checkout_success_url(),
      'cancel_url' => dashboard_checkout_cancel_url(),
      'client_reference_id' => (string) $user->ID,
      'metadata' => [
        'wp_user_id' => (string) $user->ID,
      ],
      'subscription_data' => [
        'metadata' => [
          'wp_user_id' => (string) $user->ID,
        ],
      ],
      'allow_promotion_codes' => true,
    ]);

    wp_redirect($session->url);
    exit;
  } catch (\Throwable $e) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=stripe_checkout_failed');
    exit;
  }
}

add_action('admin_post_dashboard_start_checkout', 'dashboard_handle_start_checkout');

/**
 * Öffnet das Stripe Billing Portal für bestehende Kunden.
 */
function dashboard_handle_open_billing_portal()
{
  if (!is_user_logged_in()) {
    wp_safe_redirect('/dashboard-login');
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_open_billing_portal')
  ) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=invalid_request');
    exit;
  }

  if (!dashboard_stripe_boot()) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=stripe_sdk_missing');
    exit;
  }

  $configError = dashboard_stripe_portal_config_error();

  if ($configError !== '') {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=' . rawurlencode($configError));
    exit;
  }

  $customerId = trim((string) get_user_meta(get_current_user_id(), 'stripe_customer_id', true));

  if ($customerId === '') {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=stripe_customer_missing');
    exit;
  }

  try {
    $session = \Stripe\BillingPortal\Session::create([
      'customer' => $customerId,
      'return_url' => dashboard_settings_billing_url(),
    ]);

    wp_redirect($session->url);
    exit;
  } catch (\Throwable $e) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=stripe_portal_failed');
    exit;
  }
}

add_action('admin_post_dashboard_open_billing_portal', 'dashboard_handle_open_billing_portal');

/**
 * Holt einen vorhandenen Stripe-Kunden oder legt ihn neu an.
 */
function dashboard_get_or_create_stripe_customer($user)
{
  $customerId = trim((string) get_user_meta($user->ID, 'stripe_customer_id', true));

  if ($customerId !== '') {
    try {
      $customer = \Stripe\Customer::retrieve($customerId);

      if (empty($customer->deleted)) {
        \Stripe\Customer::update($customerId, [
          'email' => $user->user_email,
          'name' => $user->display_name ?: $user->user_login,
          'metadata' => [
            'wp_user_id' => (string) $user->ID,
          ],
        ]);

        return \Stripe\Customer::retrieve($customerId);
      }

      delete_user_meta($user->ID, 'stripe_customer_id');
    } catch (\Throwable $e) {
      delete_user_meta($user->ID, 'stripe_customer_id');
    }
  }

  $customer = \Stripe\Customer::create([
    'email' => $user->user_email,
    'name' => $user->display_name ?: $user->user_login,
    'metadata' => [
      'wp_user_id' => (string) $user->ID,
    ],
  ]);

  update_user_meta($user->ID, 'stripe_customer_id', $customer->id);

  return $customer;
}

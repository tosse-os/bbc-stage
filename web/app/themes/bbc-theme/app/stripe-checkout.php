<?php

/**
 * Startet den Stripe-Checkout für eine Subscription.
 */
function dashboard_checkout_allowed_plans(): array
{
  return ['trial', 'basis', 'pro'];
}

function dashboard_checkout_optional_plan_from_value($value): string
{
  $plan = sanitize_key((string) $value);

  return in_array($plan, dashboard_checkout_allowed_plans(), true) ? $plan : '';
}

function dashboard_checkout_plan_from_value($value): string
{
  $plan = dashboard_checkout_optional_plan_from_value($value);

  return $plan !== '' ? $plan : 'basis';
}

function dashboard_checkout_plan_from_request(): string
{
  $postedPlan = dashboard_checkout_optional_plan_from_value($_POST['plan'] ?? '');

  if ($postedPlan !== '') {
    return $postedPlan;
  }

  if (is_user_logged_in()) {
    $savedPlan = dashboard_checkout_optional_plan_from_value(
      get_user_meta(get_current_user_id(), 'dashboard_selected_plan', true)
    );

    if ($savedPlan !== '') {
      return $savedPlan;
    }
  }

  return 'basis';
}

function dashboard_checkout_price_config(string $plan): array
{
  $basisPriceId = dashboard_stripe_env('STRIPE_PRICE_ID_BASIS');
  $proPriceId = dashboard_stripe_env('STRIPE_PRICE_ID_PRO');
  $trialFeePriceId = dashboard_stripe_env('STRIPE_PRICE_ID_TRIAL_FEE');

  if ($basisPriceId === '') {
    $basisPriceId = dashboard_stripe_price_id();
  }

  return match ($plan) {
    'trial' => [
      'recurring_price_id' => $basisPriceId,
      'trial_fee_price_id' => $trialFeePriceId,
      'trial_period_days' => 14,
    ],
    'pro' => [
      'recurring_price_id' => $proPriceId,
      'trial_fee_price_id' => '',
      'trial_period_days' => 0,
    ],
    default => [
      'recurring_price_id' => $basisPriceId,
      'trial_fee_price_id' => '',
      'trial_period_days' => 0,
    ],
  };
}

function dashboard_checkout_config_error_for_plan(string $plan): string
{
  if (dashboard_stripe_secret_key() === '') {
    return 'stripe_secret_missing';
  }

  $config = dashboard_checkout_price_config($plan);

  if ($plan === 'pro' && ($config['recurring_price_id'] ?? '') === '') {
    return 'stripe_price_pro_missing';
  }

  if ($plan === 'trial') {
    if (($config['recurring_price_id'] ?? '') === '') {
      return 'stripe_price_basis_missing';
    }

    if (($config['trial_fee_price_id'] ?? '') === '') {
      return 'stripe_trial_fee_missing';
    }

    return '';
  }

  if (($config['recurring_price_id'] ?? '') === '') {
    return 'stripe_price_basis_missing';
  }

  return '';
}

function dashboard_checkout_line_items_for_plan(string $plan): array
{
  $config = dashboard_checkout_price_config($plan);

  $items = [];

  if ($plan === 'trial' && ($config['trial_fee_price_id'] ?? '') !== '') {
    $items[] = [
      'price' => $config['trial_fee_price_id'],
      'quantity' => 1,
    ];
  }

  $items[] = [
    'price' => $config['recurring_price_id'],
    'quantity' => 1,
  ];

  return $items;
}

function dashboard_checkout_subscription_data_for_plan(string $plan, int $userId): array
{
  $config = dashboard_checkout_price_config($plan);

  $data = [
    'metadata' => [
      'wp_user_id' => (string) $userId,
      'dashboard_plan' => $plan,
    ],
  ];

  if (($config['trial_period_days'] ?? 0) > 0) {
    $data['trial_period_days'] = (int) $config['trial_period_days'];
  }

  return $data;
}

function dashboard_redirect_to_checkout_for_user($user, string $plan, string $successUrl, string $cancelUrl): string
{
  if (!$user || is_wp_error($user) || empty($user->ID)) {
    return 'user_missing';
  }

  if (!dashboard_stripe_boot()) {
    return 'stripe_sdk_missing';
  }

  $configError = dashboard_checkout_config_error_for_plan($plan);

  if ($configError !== '') {
    return $configError;
  }

  try {
    $customer = dashboard_get_or_create_stripe_customer($user);

    $session = \Stripe\Checkout\Session::create([
      'mode' => 'subscription',
      'customer' => $customer->id,
      'line_items' => dashboard_checkout_line_items_for_plan($plan),
      'success_url' => $successUrl,
      'cancel_url' => $cancelUrl,
      'client_reference_id' => (string) $user->ID,
      'metadata' => [
        'wp_user_id' => (string) $user->ID,
        'dashboard_plan' => $plan,
      ],
      'subscription_data' => dashboard_checkout_subscription_data_for_plan($plan, (int) $user->ID),
      'allow_promotion_codes' => true,
    ]);

    update_user_meta($user->ID, 'dashboard_selected_plan', $plan);

    wp_redirect($session->url);
    exit;
  } catch (\Throwable $e) {
    return 'stripe_checkout_failed';
  }
}

function dashboard_handle_start_checkout()
{
  if (!is_user_logged_in()) {
    wp_safe_redirect(dashboard_login_url());
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_start_checkout')
  ) {
    wp_safe_redirect(dashboard_settings_billing_url(['error' => 'invalid_request']));
    exit;
  }

  $plan = dashboard_checkout_plan_from_request();

  $error = dashboard_redirect_to_checkout_for_user(
    wp_get_current_user(),
    $plan,
    dashboard_checkout_success_url(),
    dashboard_checkout_cancel_url()
  );

  wp_safe_redirect(dashboard_settings_billing_url(['error' => $error]));
  exit;
}

add_action('admin_post_dashboard_start_checkout', 'dashboard_handle_start_checkout');

/**
 * Öffnet das Stripe Billing Portal für bestehende Kunden.
 */
function dashboard_handle_open_billing_portal()
{
  if (!is_user_logged_in()) {
    wp_safe_redirect(dashboard_login_url());
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_open_billing_portal')
  ) {
    wp_safe_redirect(dashboard_settings_billing_url(['error' => 'invalid_request']));
    exit;
  }

  if (!dashboard_stripe_boot()) {
    wp_safe_redirect(dashboard_settings_billing_url(['error' => 'stripe_sdk_missing']));
    exit;
  }

  $configError = dashboard_stripe_portal_config_error();

  if ($configError !== '') {
    wp_safe_redirect(dashboard_settings_billing_url(['error' => $configError]));
    exit;
  }

  $customerId = trim((string) get_user_meta(get_current_user_id(), 'stripe_customer_id', true));

  if ($customerId === '') {
    wp_safe_redirect(dashboard_settings_billing_url(['error' => 'stripe_customer_missing']));
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
    wp_safe_redirect(dashboard_settings_billing_url(['error' => 'stripe_portal_failed']));
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

add_action('template_redirect', 'dashboard_handle_subscribe_trial_gate');

function dashboard_handle_subscribe_trial_gate(): void
{
  if (!is_page()) {
    return;
  }

  $post = get_queried_object();

  if (!$post || ($post->post_name ?? '') !== 'subscribe-trial') {
    return;
  }

  if (!is_user_logged_in()) {
    return;
  }

  $user = wp_get_current_user();

  if (function_exists('dashboard_user_should_use_wp_admin') && dashboard_user_should_use_wp_admin($user)) {
    wp_safe_redirect(admin_url());
    exit;
  }

  wp_safe_redirect(dashboard_settings_billing_url());
  exit;
}

add_action('admin_post_dashboard_register_and_checkout', 'dashboard_handle_register_and_checkout');
add_action('admin_post_nopriv_dashboard_register_and_checkout', 'dashboard_handle_register_and_checkout');

function dashboard_handle_register_and_checkout()
{
  $plan = dashboard_checkout_plan_from_request();

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_register_checkout')
  ) {
    wp_safe_redirect('/subscribe-trial/?error=invalid_request&plan=' . rawurlencode($plan));
    exit;
  }

  if (is_user_logged_in()) {
    wp_safe_redirect(dashboard_settings_billing_url());
    exit;
  }

  if (empty($_POST['terms'])) {
    wp_safe_redirect('/subscribe-trial/?error=terms&plan=' . rawurlencode($plan));
    exit;
  }

  $email = sanitize_email($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $firstName = sanitize_text_field($_POST['first_name'] ?? '');
  $lastName = sanitize_text_field($_POST['last_name'] ?? '');
  $postedName = sanitize_text_field($_POST['name'] ?? '');
  $name = trim($firstName . ' ' . $lastName);

  if ($name === '') {
    $name = $postedName;
  }

  if (!is_email($email)) {
    wp_safe_redirect('/subscribe-trial/?error=email&plan=' . rawurlencode($plan));
    exit;
  }

  if (strlen($password) < 8) {
    wp_safe_redirect('/subscribe-trial/?error=weak_password&plan=' . rawurlencode($plan));
    exit;
  }

  if (email_exists($email)) {
    wp_safe_redirect(dashboard_login_url(['error' => 'account_exists', 'plan' => $plan]));
    exit;
  }

  if (!dashboard_stripe_boot()) {
    wp_safe_redirect('/subscribe-trial/?error=stripe_sdk_missing&plan=' . rawurlencode($plan));
    exit;
  }

  $configError = dashboard_checkout_config_error_for_plan($plan);

  if ($configError !== '') {
    wp_safe_redirect('/subscribe-trial/?error=' . rawurlencode($configError) . '&plan=' . rawurlencode($plan));
    exit;
  }

  $user_id = wp_create_user($email, $password, $email);

  if (is_wp_error($user_id)) {
    wp_safe_redirect('/subscribe-trial/?error=create_failed&plan=' . rawurlencode($plan));
    exit;
  }

  wp_update_user([
    'ID' => $user_id,
    'display_name' => $name !== '' ? $name : $email,
    'first_name' => $firstName,
    'last_name' => $lastName,
  ]);

  update_user_meta($user_id, 'dashboard_selected_plan', $plan);

  dashboard_set_subscription_state($user_id, 'payment_required');

  wp_set_current_user($user_id);
  wp_set_auth_cookie($user_id);

  $user = get_user_by('id', $user_id);

  $error = dashboard_redirect_to_checkout_for_user(
    $user,
    $plan,
    dashboard_url('dashboard', ['checkout' => 'success']),
    dashboard_settings_billing_url(['stripe' => 'cancel'])
  );

  wp_safe_redirect(dashboard_settings_billing_url(['error' => $error]));
  exit;
}

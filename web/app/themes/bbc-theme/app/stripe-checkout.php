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

function dashboard_user_has_used_trial(int $userId): bool
{
  if ($userId <= 0) {
    return true;
  }

  if (get_user_meta($userId, 'dashboard_trial_used_at', true) !== '') {
    return true;
  }

  $customerId = trim((string) get_user_meta($userId, 'stripe_customer_id', true));

  if ($customerId === '' || !dashboard_stripe_boot()) {
    return false;
  }

  try {
    $subscriptions = \Stripe\Subscription::all([
      'customer' => $customerId,
      'status' => 'all',
      'limit' => 100,
    ]);

    foreach ($subscriptions->data as $subscription) {
      if (!empty($subscription->trial_start) || !empty($subscription->trial_end)) {
        update_user_meta($userId, 'dashboard_trial_used_at', current_time('mysql'));
        return true;
      }

      if (isset($subscription->metadata) && is_object($subscription->metadata)) {
        $plan = dashboard_checkout_optional_plan_from_value($subscription->metadata->dashboard_plan ?? '');

        if ($plan === 'trial') {
          update_user_meta($userId, 'dashboard_trial_used_at', current_time('mysql'));
          return true;
        }
      }
    }
  } catch (\Throwable $e) {
    error_log('[BBC Trial Check] ' . $e->getMessage());
  }

  return false;
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

  $recurringPriceId = (string) ($config['recurring_price_id'] ?? '');
  $trialFeePriceId = (string) ($config['trial_fee_price_id'] ?? '');

  if ($plan === 'trial' && $trialFeePriceId !== '' && $trialFeePriceId !== $recurringPriceId) {
    $items[] = [
      'price' => $trialFeePriceId,
      'quantity' => 1,
    ];
  }

  if ($recurringPriceId !== '') {
    $items[] = [
      'price' => $recurringPriceId,
      'quantity' => 1,
    ];
  }

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
    error_log('[BBC Checkout Failed] ' . $e->getMessage());
    return 'stripe_checkout_failed';
  }
}

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

  $rateLimitIdentity = (string) get_current_user_id();

  if (function_exists('dashboard_rate_limit_hit') && dashboard_rate_limit_hit('dashboard_start_checkout', $rateLimitIdentity, 8, 10 * MINUTE_IN_SECONDS)) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=rate_limited');
    exit;
  }

  $plan = dashboard_checkout_plan_from_request();

  if ($plan === 'trial' && dashboard_user_has_used_trial(get_current_user_id())) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=trial_already_used');
    exit;
  }

  $error = dashboard_redirect_to_checkout_for_user(
    wp_get_current_user(),
    $plan,
    dashboard_checkout_success_url(),
    dashboard_checkout_cancel_url()
  );

  wp_safe_redirect(dashboard_settings_billing_url() . '&error=' . rawurlencode($error));
  exit;
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

add_action('template_redirect', 'dashboard_handle_checkout_success_return', 30);

function dashboard_handle_checkout_success_return(): void
{
  if (!is_user_logged_in() || !is_page()) {
    return;
  }

  $post = get_queried_object();

  if (!$post || ($post->post_name ?? '') !== 'dashboard-settings') {
    return;
  }

  if (($_GET['tab'] ?? '') !== 'billing') {
    return;
  }

  if (($_GET['stripe'] ?? '') !== 'success') {
    return;
  }

  if (isset($_GET['sync'])) {
    return;
  }

  $sessionId = sanitize_text_field(wp_unslash($_GET['session_id'] ?? ''));

  if ($sessionId !== '') {
    $syncResult = dashboard_sync_checkout_session_to_current_user($sessionId);

    wp_safe_redirect(add_query_arg([
      'tab' => 'billing',
      'stripe' => 'success',
      'sync' => $syncResult,
    ], home_url('/dashboard-settings')));

    exit;
  }

  $syncResult = dashboard_sync_current_user_subscription_by_customer();

  wp_safe_redirect(add_query_arg([
    'tab' => 'billing',
    'stripe' => 'success',
    'sync' => $syncResult,
  ], home_url('/dashboard-settings')));

  exit;
}

function dashboard_sync_checkout_session_to_current_user(string $sessionId): string
{
  if (!dashboard_stripe_boot()) {
    return 'stripe_sdk_missing';
  }

  $userId = get_current_user_id();

  try {
    $session = \Stripe\Checkout\Session::retrieve($sessionId);

    $sessionUserId = (int) ($session->client_reference_id ?? 0);

    if ($sessionUserId !== $userId) {
      return 'session_user_mismatch';
    }

    $customerId = trim((string) ($session->customer ?? ''));

    if ($customerId !== '') {
      dashboard_stripe_store_customer_id($userId, $customerId);
    }

    $plan = '';

    if (isset($session->metadata) && is_object($session->metadata)) {
      $plan = dashboard_checkout_optional_plan_from_value($session->metadata->dashboard_plan ?? '');
    }

    if ($plan !== '') {
      update_user_meta($userId, 'dashboard_selected_plan', $plan);
    }

    $subscriptionId = trim((string) ($session->subscription ?? ''));

    if ($subscriptionId === '') {
      return 'subscription_missing';
    }

    $subscription = \Stripe\Subscription::retrieve($subscriptionId);

    dashboard_stripe_sync_subscription($userId, $subscription);

    return 'subscription_synced';
  } catch (\Throwable $e) {
    error_log('[BBC Checkout Sync] ' . $e->getMessage());
    return 'sync_failed';
  }
}

function dashboard_sync_current_user_subscription_by_customer(): string
{
  if (!dashboard_stripe_boot()) {
    return 'stripe_sdk_missing';
  }

  $userId = get_current_user_id();
  $customerId = trim((string) get_user_meta($userId, 'stripe_customer_id', true));

  if ($customerId === '') {
    return 'stripe_customer_missing';
  }

  try {
    $subscriptions = \Stripe\Subscription::all([
      'customer' => $customerId,
      'status' => 'all',
      'limit' => 10,
    ]);

    $selectedSubscription = null;

    foreach ($subscriptions->data as $subscription) {
      $status = trim((string) ($subscription->status ?? ''));

      if (in_array($status, ['active', 'trialing'], true)) {
        $selectedSubscription = $subscription;
        break;
      }
    }

    if (!$selectedSubscription) {
      foreach ($subscriptions->data as $subscription) {
        $status = trim((string) ($subscription->status ?? ''));

        if (!in_array($status, ['canceled', 'incomplete_expired'], true)) {
          $selectedSubscription = $subscription;
          break;
        }
      }
    }

    if (!$selectedSubscription) {
      return 'subscription_missing';
    }

    dashboard_stripe_sync_subscription($userId, $selectedSubscription);

    return 'subscription_synced';
  } catch (\Throwable $e) {
    error_log('[BBC Customer Sync] ' . $e->getMessage());
    return 'sync_failed';
  }
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

  $plan = dashboard_checkout_optional_plan_from_value($_GET['plan'] ?? '');

  if ($plan === 'trial' && dashboard_user_has_used_trial($user->ID)) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=trial_already_used');
    exit;
  }

  if ($plan !== '' && $plan !== 'trial') {
    update_user_meta($user->ID, 'dashboard_selected_plan', $plan);
  }

  $billingUrl = dashboard_settings_billing_url();

  if ($plan !== '' && $plan !== 'trial') {
    $billingUrl = add_query_arg('plan', $plan, $billingUrl);
  }

  wp_safe_redirect($billingUrl);
  exit;
}

add_action('template_redirect', 'dashboard_capture_billing_plan_from_query', 20);

function dashboard_capture_billing_plan_from_query(): void
{
  if (!is_user_logged_in() || !is_page()) {
    return;
  }

  $post = get_queried_object();

  if (!$post || ($post->post_name ?? '') !== 'dashboard-settings') {
    return;
  }

  if (($_GET['tab'] ?? '') !== 'billing') {
    return;
  }

  $plan = dashboard_checkout_optional_plan_from_value($_GET['plan'] ?? '');

  if ($plan === '' || $plan === 'trial') {
    return;
  }

  update_user_meta(get_current_user_id(), 'dashboard_selected_plan', $plan);
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

  $rateLimitEmail = sanitize_email($_POST['email'] ?? '');
  $rateLimitIp = function_exists('dashboard_request_ip') ? dashboard_request_ip() : ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
  $rateLimitIdentity = is_user_logged_in() ? (string) get_current_user_id() : ($rateLimitIp . '|' . strtolower($rateLimitEmail));

  if (function_exists('dashboard_rate_limit_hit') && dashboard_rate_limit_hit('dashboard_register_checkout', $rateLimitIdentity, 5, 15 * MINUTE_IN_SECONDS)) {
    wp_safe_redirect('/subscribe-trial/?error=rate_limited&plan=' . rawurlencode($plan));
    exit;
  }

  if (is_user_logged_in()) {
    if ($plan === 'trial' && dashboard_user_has_used_trial(get_current_user_id())) {
      wp_safe_redirect('/subscribe-trial/?error=trial_already_used&plan=basis');
      exit;
    }

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
    wp_safe_redirect('/dashboard-login?error=account_exists&plan=' . rawurlencode($plan));
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
  wp_set_auth_cookie($user_id, true, is_ssl());

  $user = get_user_by('id', $user_id);

  $error = dashboard_redirect_to_checkout_for_user(
    $user,
    $plan,
    home_url('/dashboard?checkout=success'),
    home_url('/dashboard-settings?tab=billing&stripe=cancel')
  );

  wp_safe_redirect(dashboard_settings_billing_url() . '&error=' . rawurlencode($error));
  exit;
}

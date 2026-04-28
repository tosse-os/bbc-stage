<?php

if (!function_exists('dashboard_stripe_boot_sdk')) {
  function dashboard_stripe_boot_sdk(): bool
  {
    if (class_exists(\Stripe\Stripe::class)) {
      return true;
    }

    $autoload = dirname(__DIR__) . '/vendor/autoload.php';

    if (file_exists($autoload)) {
      require_once $autoload;
    }

    return class_exists(\Stripe\Stripe::class);
  }
}

if (!function_exists('dashboard_stripe_env')) {
  function dashboard_stripe_env(string $key, $default = '')
  {
    if (function_exists('env')) {
      $value = env($key);
      if ($value !== null && $value !== '') {
        return is_string($value) ? trim($value) : $value;
      }
    }

    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
      return $default;
    }

    return is_string($value) ? trim($value) : $value;
  }
}

if (!function_exists('dashboard_stripe_config')) {
  function dashboard_stripe_config(): array
  {
    return [
      'publishable_key' => (string) dashboard_stripe_env('STRIPE_PUBLISHABLE_KEY', ''),
      'secret_key' => (string) dashboard_stripe_env('STRIPE_SECRET_KEY', ''),
      'price_id' => (string) dashboard_stripe_env('STRIPE_PRICE_ID', ''),
      'webhook_secret' => (string) dashboard_stripe_env('STRIPE_WEBHOOK_SECRET', ''),
    ];
  }
}

if (!function_exists('dashboard_stripe_billing_url')) {
  function dashboard_stripe_billing_url(array $args = []): string
  {
    $base = home_url('/dashboard-settings');
    $args = array_merge(['tab' => 'billing'], $args);
    return add_query_arg($args, $base);
  }
}

add_action('admin_post_dashboard_start_checkout', function () {
  if (!is_user_logged_in()) {
    wp_safe_redirect(home_url('/dashboard-login'));
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_start_checkout')
  ) {
    wp_safe_redirect(dashboard_stripe_billing_url(['error' => 'invalid_request']));
    exit;
  }

  if (!dashboard_stripe_boot_sdk()) {
    wp_safe_redirect(dashboard_stripe_billing_url(['error' => 'stripe_sdk_missing']));
    exit;
  }

  $config = dashboard_stripe_config();

  if ($config['secret_key'] === '' || $config['price_id'] === '') {
    wp_safe_redirect(dashboard_stripe_billing_url(['error' => 'stripe_not_configured']));
    exit;
  }

  try {
    \Stripe\Stripe::setApiKey($config['secret_key']);

    $user = wp_get_current_user();
    $user_id = (int) $user->ID;

    $customer_id = (string) get_user_meta($user_id, 'stripe_customer_id', true);

    if ($customer_id === '') {
      $customer = \Stripe\Customer::create([
        'email' => $user->user_email,
        'name' => $user->display_name ?: $user->user_email,
        'metadata' => [
          'user_id' => (string) $user_id,
        ],
      ]);

      $customer_id = (string) $customer->id;

      update_user_meta($user_id, 'stripe_customer_id', $customer_id);
    }

    $session = \Stripe\Checkout\Session::create([
      'mode' => 'subscription',
      'customer' => $customer_id,
      'line_items' => [
        [
          'price' => $config['price_id'],
          'quantity' => 1,
        ],
      ],
      'client_reference_id' => (string) $user_id,
      'metadata' => [
        'user_id' => (string) $user_id,
      ],
      'subscription_data' => [
        'metadata' => [
          'user_id' => (string) $user_id,
        ],
      ],
      'allow_promotion_codes' => true,
      'success_url' => dashboard_stripe_billing_url([
        'stripe' => 'success',
        'session_id' => '{CHECKOUT_SESSION_ID}',
      ]),
      'cancel_url' => dashboard_stripe_billing_url([
        'stripe' => 'cancel',
      ]),
    ]);

    wp_safe_redirect($session->url);
    exit;
  } catch (\Throwable $e) {
    error_log('Stripe checkout error: ' . $e->getMessage());
    wp_safe_redirect(dashboard_stripe_billing_url(['error' => 'stripe_checkout_failed']));
    exit;
  }
});

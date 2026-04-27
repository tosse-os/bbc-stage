<?php

/**
 * Registriert den Checkout-Start für eingeloggte Dashboard-User.
 * Erstellt eine Stripe Checkout Session für das Abo.
 */
add_action('admin_post_dashboard_start_checkout', __NAMESPACE__ . '\\dashboard_start_checkout');

/**
 * Liest einen Stripe-Umgebungswert aus der Server-Konfiguration.
 * Nutzt die gesetzten ENV-Werte aus Bedrock.
 */
function stripe_checkout_env(string $key): string
{
  $value = getenv($key);

  return is_string($value) ? trim($value) : '';
}

/**
 * Liefert den Stripe-Client für serverseitige Checkout-Aktionen.
 * Verwendet den Secret Key aus der Umgebung.
 */
function stripe_checkout_client(): \Stripe\StripeClient
{
  return new \Stripe\StripeClient(stripe_checkout_env('STRIPE_SECRET_KEY'));
}

/**
 * Liefert oder erstellt den Stripe Customer des aktuellen Users.
 * Verknüpft den WordPress-User dauerhaft mit einer Stripe Customer ID.
 */
function stripe_checkout_customer_id(int $user_id): string
{
  $existing = (string) get_user_meta($user_id, 'stripe_customer_id', true);

  if ($existing !== '') {
    return $existing;
  }

  $user = get_userdata($user_id);

  if (!$user) {
    return '';
  }

  $customer = stripe_checkout_client()->customers->create([
    'email' => $user->user_email,
    'name' => $user->display_name ?: $user->user_login,
    'metadata' => [
      'user_id' => (string) $user_id,
    ],
  ]);

  update_user_meta($user_id, 'stripe_customer_id', (string) $customer->id);

  return (string) $customer->id;
}

/**
 * Startet den Stripe Checkout für das Dashboard-Abo.
 * Prüft Login, Nonce und Konfiguration und leitet dann zu Stripe weiter.
 */
function dashboard_start_checkout(): void
{
  if (!is_user_logged_in()) {
    wp_safe_redirect(home_url('/dashboard-login'));
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce(wp_unslash($_POST['_wpnonce']), 'dashboard_start_checkout')
  ) {
    wp_safe_redirect(home_url('/dashboard-settings?tab=billing&error=invalid_request'));
    exit;
  }

  $secretKey = stripe_checkout_env('STRIPE_SECRET_KEY');
  $priceId = stripe_checkout_env('STRIPE_PRICE_ID');

  if ($secretKey === '' || $priceId === '') {
    wp_safe_redirect(home_url('/dashboard-settings?tab=billing&error=stripe_not_configured'));
    exit;
  }

  $user_id = get_current_user_id();
  $customerId = stripe_checkout_customer_id($user_id);

  if ($customerId === '') {
    wp_safe_redirect(home_url('/dashboard-settings?tab=billing&error=customer_failed'));
    exit;
  }

  try {
    $session = stripe_checkout_client()->checkout->sessions->create([
      'mode' => 'subscription',
      'customer' => $customerId,
      'line_items' => [[
        'price' => $priceId,
        'quantity' => 1,
      ]],
      'client_reference_id' => (string) $user_id,
      'metadata' => [
        'user_id' => (string) $user_id,
      ],
      'subscription_data' => [
        'metadata' => [
          'user_id' => (string) $user_id,
        ],
      ],
      'success_url' => home_url('/dashboard-settings?tab=billing&stripe=success'),
      'cancel_url' => home_url('/dashboard-settings?tab=billing&stripe=cancel'),
    ]);
  } catch (\Throwable $exception) {
    wp_safe_redirect(home_url('/dashboard-settings?tab=billing&error=checkout_failed'));
    exit;
  }

  if (empty($session->url)) {
    wp_safe_redirect(home_url('/dashboard-settings?tab=billing&error=checkout_missing_url'));
    exit;
  }

  wp_redirect((string) $session->url);
  exit;
}

<?php

/**
 * Liefert die Billing-URL des Dashboards.
 */
if (!function_exists('dashboard_settings_billing_url')) {
  function dashboard_settings_billing_url(): string
  {
    return home_url('/dashboard-settings?tab=billing');
  }
}

/**
 * Liefert die Success-URL nach erfolgreichem Checkout.
 */
function dashboard_checkout_success_url(): string
{
  return home_url('/dashboard-settings?tab=billing&stripe=success');
}

/**
 * Liefert die Cancel-URL nach abgebrochenem Checkout.
 */
function dashboard_checkout_cancel_url(): string
{
  return home_url('/dashboard-settings?tab=billing&stripe=cancel');
}

/**
 * Liest einen Stripe-ENV-Wert robust aus.
 */
function dashboard_stripe_env(string $key): string
{
  if (defined($key)) {
    return trim((string) constant($key));
  }

  $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

  if ($value === false || $value === null) {
    return '';
  }

  return trim((string) $value);
}

/**
 * Liefert den Pfad zum Composer-Autoloader des Themes.
 */
function dashboard_stripe_vendor_autoload_path(): string
{
  return get_stylesheet_directory() . '/vendor/autoload.php';
}

function dashboard_stripe_boot(): bool
{
  static $booted = false;
  static $ok = false;

  error_log('Stripe DEBUG __DIR__: ' . __DIR__);
  error_log('Stripe DEBUG stylesheet: ' . get_stylesheet_directory());
  error_log('Stripe DEBUG template: ' . get_template_directory());
  error_log('Stripe DEBUG ABSPATH: ' . ABSPATH);
  error_log('Stripe DEBUG autoload: ' . dashboard_stripe_vendor_autoload_path());

  if ($booted) {
    return $ok;
  }

  $booted = true;

  $autoload = dashboard_stripe_vendor_autoload_path();

  error_log('Stripe autoload path: ' . $autoload);
  error_log('Stripe autoload exists: ' . (file_exists($autoload) ? 'yes' : 'no'));
  error_log('Stripe autoload readable: ' . (is_readable($autoload) ? 'yes' : 'no'));

  if (!file_exists($autoload)) {
    return false;
  }

  require_once $autoload;

  error_log('Stripe class exists: ' . (class_exists('\Stripe\Stripe') ? 'yes' : 'no'));

  if (!class_exists('\Stripe\Stripe')) {
    return false;
  }

  $secretKey = dashboard_stripe_secret_key();

  error_log('Stripe secret key set: ' . ($secretKey !== '' ? 'yes' : 'no'));

  if ($secretKey !== '') {
    \Stripe\Stripe::setApiKey($secretKey);
  }

  $ok = true;

  return true;
}

/**
 * Liefert den Stripe Publishable Key.
 */
function dashboard_stripe_publishable_key(): string
{
  return dashboard_stripe_env('STRIPE_PUBLISHABLE_KEY');
}

/**
 * Liefert den Stripe Secret Key.
 */
function dashboard_stripe_secret_key(): string
{
  return dashboard_stripe_env('STRIPE_SECRET_KEY');
}

/**
 * Liefert die Stripe Price ID.
 */
function dashboard_stripe_price_id(): string
{
  return dashboard_stripe_env('STRIPE_PRICE_ID');
}

/**
 * Liefert das Stripe Webhook Secret.
 */
function dashboard_stripe_webhook_secret(): string
{
  return dashboard_stripe_env('STRIPE_WEBHOOK_SECRET');
}

/**
 * Prüft die Checkout-Konfiguration.
 */
function dashboard_stripe_checkout_config_error(): string
{
  if (dashboard_stripe_secret_key() === '' || dashboard_stripe_price_id() === '') {
    return 'stripe_not_configured';
  }

  return '';
}

/**
 * Prüft die Webhook-Konfiguration.
 */
function dashboard_stripe_webhook_config_error(): string
{
  if (dashboard_stripe_secret_key() === '' || dashboard_stripe_webhook_secret() === '') {
    return 'stripe_webhook_not_configured';
  }

  return '';
}

/**
 * Prüft die Billing-Portal-Konfiguration.
 */
function dashboard_stripe_portal_config_error(): string
{
  if (dashboard_stripe_secret_key() === '') {
    return 'stripe_secret_missing';
  }

  return '';
}

/**
 * Wandelt interne Billing-Fehlercodes in lesbare Meldungen um.
 */
function dashboard_stripe_billing_error_message(string $error): string
{
  return match ($error) {
    'invalid_request' => 'Die Anfrage war ungültig.',
    'stripe_sdk_missing' => 'Das Stripe SDK fehlt. Bitte composer require stripe/stripe-php im Theme ausführen.',
    'stripe_not_configured' => 'Stripe ist noch nicht vollständig konfiguriert.',
    'stripe_secret_missing' => 'Der Stripe Secret Key fehlt.',
    'stripe_price_basis_missing' => 'Die Stripe Price ID für das Basis-Abo fehlt.',
    'stripe_price_pro_missing' => 'Die Stripe Price ID für das Pro-Abo fehlt.',
    'stripe_trial_fee_missing' => 'Die Stripe Price ID für die Trial-Gebühr fehlt.',
    'stripe_webhook_not_configured' => 'Das Stripe Webhook Secret fehlt.',
    'stripe_customer_missing' => 'Kein Stripe-Kunde gefunden.',
    'stripe_checkout_failed' => 'Stripe Checkout konnte nicht gestartet werden.',
    'stripe_portal_failed' => 'Das Billing Portal konnte nicht geöffnet werden.',
    default => 'Stripe Checkout konnte nicht gestartet werden.',
  };
}

/**
 * Wandelt den internen Subscription-State in ein Label um.
 */
function dashboard_stripe_subscription_state_label(string $state): string
{
  return match ($state) {
    'trial' => 'Trial',
    'active' => 'Active',
    'payment_required' => 'Payment required',
    'past_due' => 'Past due',
    'canceled' => 'Canceled',
    default => ucfirst(str_replace('_', ' ', $state)),
  };
}

/**
 * Extrahiert eine WordPress-User-ID aus Stripe-Metadaten.
 */
function dashboard_stripe_extract_wp_user_id($object): int
{
  if (!is_object($object)) {
    return 0;
  }

  if (!isset($object->metadata) || !is_object($object->metadata)) {
    return 0;
  }

  $userId = (int) ($object->metadata->wp_user_id ?? 0);

  return $userId > 0 ? $userId : 0;
}

/**
 * Findet einen User anhand der Stripe Customer ID.
 */
function dashboard_stripe_find_user_by_customer_id(string $customerId)
{
  if ($customerId === '') {
    return null;
  }

  $users = get_users([
    'number' => 1,
    'meta_key' => 'stripe_customer_id',
    'meta_value' => $customerId,
  ]);

  if (empty($users)) {
    return null;
  }

  return $users[0];
}

/**
 * Findet einen User anhand der E-Mail-Adresse.
 */
function dashboard_stripe_find_user_by_email(string $email)
{
  if ($email === '') {
    return null;
  }

  $user = get_user_by('email', $email);

  return $user ?: null;
}

/**
 * Holt die Customer-E-Mail aus Stripe.
 */
function dashboard_stripe_get_customer_email(string $customerId): string
{
  if ($customerId === '') {
    return '';
  }

  if (!dashboard_stripe_boot()) {
    return '';
  }

  try {
    $customer = \Stripe\Customer::retrieve($customerId);

    if (!empty($customer->deleted)) {
      return '';
    }

    return trim((string) ($customer->email ?? ''));
  } catch (\Throwable $e) {
    return '';
  }
}

/**
 * Findet den User aus einer Checkout-Session.
 */
function dashboard_stripe_find_user_from_checkout_session($session)
{
  if (!is_object($session)) {
    return null;
  }

  $clientReferenceId = (int) ($session->client_reference_id ?? 0);

  if ($clientReferenceId > 0) {
    $user = get_user_by('id', $clientReferenceId);

    if ($user) {
      return $user;
    }
  }

  $customerId = trim((string) ($session->customer ?? ''));

  if ($customerId !== '') {
    $user = dashboard_stripe_find_user_by_customer_id($customerId);

    if ($user) {
      return $user;
    }
  }

  $email = trim((string) ($session->customer_email ?? ''));

  if ($email === '' && isset($session->customer_details) && is_object($session->customer_details)) {
    $email = trim((string) ($session->customer_details->email ?? ''));
  }

  if ($email !== '') {
    return dashboard_stripe_find_user_by_email($email);
  }

  return null;
}

/**
 * Findet den User aus einem Subscription-Objekt.
 */
function dashboard_stripe_find_user_from_subscription($subscription)
{
  if (!is_object($subscription)) {
    return null;
  }

  $userId = dashboard_stripe_extract_wp_user_id($subscription);

  if ($userId > 0) {
    $user = get_user_by('id', $userId);

    if ($user) {
      return $user;
    }
  }

  $customerId = trim((string) ($subscription->customer ?? ''));

  if ($customerId !== '') {
    $user = dashboard_stripe_find_user_by_customer_id($customerId);

    if ($user) {
      return $user;
    }

    $email = dashboard_stripe_get_customer_email($customerId);

    if ($email !== '') {
      return dashboard_stripe_find_user_by_email($email);
    }
  }

  return null;
}

/**
 * Findet den User aus einem Invoice-Objekt.
 */
function dashboard_stripe_find_user_from_invoice($invoice)
{
  if (!is_object($invoice)) {
    return null;
  }

  $userId = dashboard_stripe_extract_wp_user_id($invoice);

  if ($userId > 0) {
    $user = get_user_by('id', $userId);

    if ($user) {
      return $user;
    }
  }

  $customerId = trim((string) ($invoice->customer ?? ''));

  if ($customerId !== '') {
    $user = dashboard_stripe_find_user_by_customer_id($customerId);

    if ($user) {
      return $user;
    }
  }

  $email = trim((string) ($invoice->customer_email ?? ''));

  if ($email !== '') {
    return dashboard_stripe_find_user_by_email($email);
  }

  if ($customerId !== '') {
    $email = dashboard_stripe_get_customer_email($customerId);

    if ($email !== '') {
      return dashboard_stripe_find_user_by_email($email);
    }
  }

  return null;
}

/**
 * Speichert eine Stripe Customer ID am User.
 */
function dashboard_stripe_store_customer_id(int $userId, string $customerId): void
{
  if ($userId <= 0 || $customerId === '') {
    return;
  }

  update_user_meta($userId, 'stripe_customer_id', $customerId);
}

/**
 * Synchronisiert Stripe-Subscriptions in User-Meta und Access-State.
 */
function dashboard_stripe_sync_subscription(int $userId, $subscription): void
{
  if ($userId <= 0 || !is_object($subscription)) {
    return;
  }

  $customerId = trim((string) ($subscription->customer ?? ''));
  $subscriptionId = trim((string) ($subscription->id ?? ''));
  $status = trim((string) ($subscription->status ?? ''));

  if ($customerId !== '') {
    update_user_meta($userId, 'stripe_customer_id', $customerId);
  }

  if ($subscriptionId !== '') {
    update_user_meta($userId, 'stripe_subscription_id', $subscriptionId);
  }

  if ($status !== '') {
    update_user_meta($userId, 'stripe_subscription_status', $status);
  }

  if (isset($subscription->current_period_end)) {
    update_user_meta($userId, 'stripe_current_period_end', (int) $subscription->current_period_end);
  }

  update_user_meta(
    $userId,
    'stripe_cancel_at_period_end',
    !empty($subscription->cancel_at_period_end) ? '1' : '0'
  );

  if (in_array($status, ['active', 'trialing'], true)) {
    dashboard_set_subscription_state($userId, 'active');
    return;
  }

  if (in_array($status, ['past_due', 'unpaid', 'incomplete', 'incomplete_expired'], true)) {
    dashboard_set_subscription_state($userId, 'past_due');
    return;
  }

  if ($status === 'canceled') {
    dashboard_set_subscription_state($userId, 'canceled');
    return;
  }

  dashboard_set_subscription_state($userId, 'payment_required');
}

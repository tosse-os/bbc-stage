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

  if ($booted) {
    return $ok;
  }

  $booted = true;

  $autoload = dashboard_stripe_vendor_autoload_path();

  if (!file_exists($autoload)) {
    return false;
  }

  require_once $autoload;

  if (!class_exists('\Stripe\Stripe')) {
    return false;
  }

  $secretKey = dashboard_stripe_secret_key();

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

function dashboard_stripe_subscription_metadata_plan($subscription): string
{
  if (!is_object($subscription) || !isset($subscription->metadata) || !is_object($subscription->metadata)) {
    return '';
  }

  $plan = sanitize_key((string) ($subscription->metadata->dashboard_plan ?? ''));

  return in_array($plan, ['trial', 'basis', 'pro'], true) ? $plan : '';
}

function dashboard_stripe_subscription_price_id($subscription): string
{
  if (!is_object($subscription)) {
    return '';
  }

  if (
    isset($subscription->items) &&
    is_object($subscription->items) &&
    isset($subscription->items->data) &&
    is_array($subscription->items->data) &&
    !empty($subscription->items->data[0]) &&
    isset($subscription->items->data[0]->price) &&
    is_object($subscription->items->data[0]->price)
  ) {
    return trim((string) ($subscription->items->data[0]->price->id ?? ''));
  }

  return '';
}

function dashboard_stripe_plan_from_price_id(string $priceId): string
{
  $priceId = trim($priceId);

  if ($priceId === '') {
    return '';
  }

  $basisPriceId = dashboard_stripe_env('STRIPE_PRICE_ID_BASIS');
  $proPriceId = dashboard_stripe_env('STRIPE_PRICE_ID_PRO');

  if ($basisPriceId === '') {
    $basisPriceId = dashboard_stripe_price_id();
  }

  if ($basisPriceId !== '' && $priceId === $basisPriceId) {
    return 'basis';
  }

  if ($proPriceId !== '' && $priceId === $proPriceId) {
    return 'pro';
  }

  return '';
}

function dashboard_stripe_format_amount(int $amount, string $currency): string
{
  $currency = strtoupper($currency ?: 'EUR');

  return number_format($amount / 100, 2, ',', '.') . ' ' . $currency;
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
  $metadataPlan = dashboard_stripe_subscription_metadata_plan($subscription);
  $priceId = dashboard_stripe_subscription_price_id($subscription);
  $currentPlan = dashboard_stripe_plan_from_price_id($priceId);

  if ($customerId !== '') {
    update_user_meta($userId, 'stripe_customer_id', $customerId);
  }

  if ($subscriptionId !== '') {
    update_user_meta($userId, 'stripe_subscription_id', $subscriptionId);
  }

  if ($status !== '') {
    update_user_meta($userId, 'stripe_subscription_status', $status);
  }

  if ($priceId !== '') {
    update_user_meta($userId, 'stripe_subscription_price_id', $priceId);
  }

  if ($currentPlan !== '') {
    update_user_meta($userId, 'dashboard_current_plan', $currentPlan);
  } elseif (in_array($metadataPlan, ['basis', 'pro'], true)) {
    update_user_meta($userId, 'dashboard_current_plan', $metadataPlan);
  }

  if ($metadataPlan !== '') {
    update_user_meta($userId, 'dashboard_selected_plan', $metadataPlan);
  }

  if (isset($subscription->current_period_start)) {
    update_user_meta($userId, 'stripe_current_period_start', (int) $subscription->current_period_start);
  }

  if (isset($subscription->current_period_end)) {
    update_user_meta($userId, 'stripe_current_period_end', (int) $subscription->current_period_end);
  }

  if (isset($subscription->trial_start)) {
    update_user_meta($userId, 'stripe_trial_start', (int) $subscription->trial_start);
  }

  if (isset($subscription->trial_end)) {
    update_user_meta($userId, 'stripe_trial_end', (int) $subscription->trial_end);
  }

  update_user_meta(
    $userId,
    'stripe_cancel_at_period_end',
    !empty($subscription->cancel_at_period_end) ? '1' : '0'
  );

  if (
    ($metadataPlan === 'trial' || get_user_meta($userId, 'dashboard_selected_plan', true) === 'trial') &&
    (int) ($subscription->trial_end ?? 0) > 0 &&
    get_user_meta($userId, 'dashboard_trial_used_at', true) === ''
  ) {
    update_user_meta($userId, 'dashboard_trial_used_at', current_time('mysql'));
  }

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

function dashboard_stripe_sync_user_subscription_from_stripe(int $userId, bool $force = false): bool
{
  if ($userId <= 0) {
    return false;
  }

  $subscriptionId = trim((string) get_user_meta($userId, 'stripe_subscription_id', true));

  if ($subscriptionId === '') {
    return false;
  }

  $transientKey = 'dashboard_stripe_sync_' . $userId;

  if (!$force && get_transient($transientKey)) {
    return false;
  }

  if (!dashboard_stripe_boot()) {
    return false;
  }

  try {
    $subscription = \Stripe\Subscription::retrieve($subscriptionId);

    if (!is_object($subscription) || !empty($subscription->deleted)) {
      return false;
    }

    dashboard_stripe_sync_subscription($userId, $subscription);

    set_transient($transientKey, 1, 20);

    return true;
  } catch (\Throwable $e) {
    error_log('[BBC Stripe Sync] Pull sync failed for user ' . $userId . ': ' . $e->getMessage());
    return false;
  }
}

function dashboard_stripe_get_user_invoices(int $userId, int $limit = 10, bool $force = false): array
{
  if ($userId <= 0) {
    return [];
  }

  $customerId = trim((string) get_user_meta($userId, 'stripe_customer_id', true));

  if ($customerId === '') {
    return [];
  }

  $cacheKey = 'dashboard_stripe_invoices_' . $userId;

  if (!$force) {
    $cached = get_transient($cacheKey);

    if (is_array($cached)) {
      return $cached;
    }
  }

  if (!dashboard_stripe_boot()) {
    return [];
  }

  try {
    $result = \Stripe\Invoice::all([
      'customer' => $customerId,
      'limit' => max(1, min(20, $limit)),
    ]);

    $items = [];

    foreach (($result->data ?? []) as $invoice) {
      $amount = (int) ($invoice->amount_paid ?? $invoice->total ?? $invoice->amount_due ?? 0);
      $currency = (string) ($invoice->currency ?? 'eur');
      $status = (string) ($invoice->status ?? '');
      $created = (int) ($invoice->created ?? 0);
      $url = (string) ($invoice->hosted_invoice_url ?? '');
      $pdf = (string) ($invoice->invoice_pdf ?? '');
      $number = (string) ($invoice->number ?? '');

      $items[] = [
        'created' => $created,
        'amount' => dashboard_stripe_format_amount($amount, $currency),
        'status' => $status !== '' ? $status : '—',
        'url' => $url,
        'pdf' => $pdf,
        'number' => $number !== '' ? $number : '—',
      ];
    }

    set_transient($cacheKey, $items, 20);

    return $items;
  } catch (\Throwable $e) {
    error_log('[BBC Stripe Sync] Invoice fetch failed for user ' . $userId . ': ' . $e->getMessage());
    return [];
  }
}

add_action('admin_post_dashboard_sync_billing', function () {
  if (!is_user_logged_in()) {
    wp_safe_redirect('/dashboard-login');
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_sync_billing')
  ) {
    wp_safe_redirect(dashboard_settings_billing_url() . '&error=invalid_request');
    exit;
  }

  $userId = get_current_user_id();

  dashboard_stripe_sync_user_subscription_from_stripe($userId, true);
  dashboard_stripe_get_user_invoices($userId, 10, true);

  delete_transient('dashboard_stripe_sync_' . $userId);
  delete_transient('dashboard_stripe_invoices_' . $userId);

  wp_safe_redirect(dashboard_settings_billing_url() . '&synced=1');
  exit;
});

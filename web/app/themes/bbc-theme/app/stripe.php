<?php

/**
 * Liefert die Billing-URL des Dashboards.
 */
if (!function_exists('dashboard_settings_billing_url')) {
  function dashboard_settings_billing_url(): string
  {
    return dashboard_settings_url(['tab' => 'billing']);
  }
}

/**
 * Liefert die Success-URL nach erfolgreichem Checkout.
 */
function dashboard_checkout_success_url(): string
{
  return dashboard_settings_url(['tab' => 'billing', 'stripe' => 'success']);
}

/**
 * Liefert die Cancel-URL nach abgebrochenem Checkout.
 */
function dashboard_checkout_cancel_url(): string
{
  return dashboard_settings_url(['tab' => 'billing', 'stripe' => 'cancel']);
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
  return dashboard_error_text($error, 'errors.stripe_checkout_failed');
}

/**
 * Wandelt den internen Subscription-State in ein Label um.
 */
function dashboard_stripe_subscription_state_label(string $state): string
{
  $label = dashboard_t('billing.states.' . $state);

  return $label !== 'billing.states.' . $state ? $label : ucfirst(str_replace('_', ' ', $state));
}

/**
 * Speichert den bei Vertragsabschluss gebundenen Stripe-Preis als User-Meta.
 */
function dashboard_stripe_sync_subscription_price(int $userId, $subscription): void
{
  if ($userId <= 0 || !is_object($subscription)) {
    return;
  }

  $items = $subscription->items->data ?? [];
  $item = is_array($items) ? ($items[0] ?? null) : null;
  $price = is_object($item) ? ($item->price ?? null) : null;

  if (!is_object($price)) {
    return;
  }

  $priceId = trim((string) ($price->id ?? ''));
  $productId = is_string($price->product ?? null)
    ? trim((string) $price->product)
    : trim((string) ($price->product->id ?? ''));
  $unitAmount = isset($price->unit_amount) ? (int) $price->unit_amount : 0;
  $currency = strtolower(trim((string) ($price->currency ?? '')));
  $interval = trim((string) ($price->recurring->interval ?? ''));
  $intervalCount = isset($price->recurring->interval_count)
    ? max(1, (int) $price->recurring->interval_count)
    : 1;

  if ($priceId !== '') {
    update_user_meta($userId, 'stripe_price_id', $priceId);
  }

  if ($productId !== '') {
    update_user_meta($userId, 'stripe_product_id', $productId);
  }

  if ($unitAmount > 0) {
    update_user_meta($userId, 'stripe_recurring_amount', $unitAmount);
  }

  if ($currency !== '') {
    update_user_meta($userId, 'stripe_currency', $currency);
  }

  if ($interval !== '') {
    update_user_meta($userId, 'stripe_interval', $interval);
    update_user_meta($userId, 'stripe_interval_count', $intervalCount);
  }
}

/**
 * Speichert den beim Trial-Checkout verwendeten einmaligen Stripe-Preis als User-Meta.
 */
function dashboard_stripe_sync_trial_fee_price(int $userId, string $priceId, int $trialDays): void
{
  $priceId = trim($priceId);

  if ($userId <= 0 || $priceId === '' || $trialDays <= 0 || !dashboard_stripe_boot()) {
    return;
  }

  try {
    $price = \Stripe\Price::retrieve($priceId);
  } catch (\Throwable $e) {
    return;
  }

  $amount = isset($price->unit_amount) ? (int) $price->unit_amount : 0;
  $currency = strtolower(trim((string) ($price->currency ?? '')));

  if ($amount <= 0 || $currency === '') {
    return;
  }

  update_user_meta($userId, 'stripe_trial_fee_price_id', $priceId);
  update_user_meta($userId, 'stripe_trial_fee_amount', $amount);
  update_user_meta($userId, 'stripe_trial_fee_currency', $currency);
  update_user_meta($userId, 'stripe_trial_days', $trialDays);
}

/**
 * Übernimmt Trial-Daten aus Stripe-Metadaten in den lokalen Preis-Snapshot.
 */
function dashboard_stripe_sync_trial_fee_from_metadata(int $userId, $object): void
{
  if ($userId <= 0 || !is_object($object) || !isset($object->metadata) || !is_object($object->metadata)) {
    return;
  }

  $plan = trim((string) ($object->metadata->dashboard_plan ?? ''));
  $priceId = trim((string) ($object->metadata->trial_fee_price_id ?? ''));
  $trialDays = (int) ($object->metadata->trial_period_days ?? 0);

  if ($plan !== 'trial' || $priceId === '' || $trialDays <= 0) {
    return;
  }

  dashboard_stripe_sync_trial_fee_price($userId, $priceId, $trialDays);
}

/**
 * Formatiert einen einmaligen Stripe-Betrag ohne Abrechnungsintervall.
 */
function dashboard_stripe_format_amount(int $amount, string $currency): string
{
  $currency = strtolower(trim($currency));

  if ($amount <= 0 || $currency === '') {
    return '';
  }

  $language = function_exists('dashboard_lang') ? dashboard_lang() : 'de';
  $number = number_format($amount / 100, 2, $language === 'de' ? ',' : '.', $language === 'de' ? '.' : ',');
  $currencyLabel = match ($currency) {
    'eur' => '€',
    'usd' => '$',
    'gbp' => '£',
    default => strtoupper($currency),
  };

  return $language === 'de'
    ? $number . ' ' . $currencyLabel
    : $currencyLabel . $number;
}

/**
 * Formatiert die beim Trial-Abschluss gespeicherte einmalige Gebühr.
 */
function dashboard_stripe_user_trial_fee_label(int $userId): string
{
  $amount = (int) get_user_meta($userId, 'stripe_trial_fee_amount', true);
  $currency = (string) get_user_meta($userId, 'stripe_trial_fee_currency', true);

  if ($amount <= 0 || trim($currency) === '') {
    $selectedPlan = trim((string) get_user_meta($userId, 'dashboard_selected_plan', true));

    if ($selectedPlan === 'trial' && function_exists('dashboard_checkout_price_config')) {
      $config = dashboard_checkout_price_config('trial');
      dashboard_stripe_sync_trial_fee_price(
        $userId,
        (string) ($config['trial_fee_price_id'] ?? ''),
        (int) ($config['trial_period_days'] ?? 0)
      );

      $amount = (int) get_user_meta($userId, 'stripe_trial_fee_amount', true);
      $currency = (string) get_user_meta($userId, 'stripe_trial_fee_currency', true);
    }
  }

  return dashboard_stripe_format_amount($amount, $currency);
}

/**
 * Formatiert den am Benutzer gespeicherten Stripe-Abopreis.
 */
function dashboard_stripe_user_price_label(int $userId): string
{
  $amount = (int) get_user_meta($userId, 'stripe_recurring_amount', true);
  $currency = strtolower(trim((string) get_user_meta($userId, 'stripe_currency', true)));
  $interval = trim((string) get_user_meta($userId, 'stripe_interval', true));
  $intervalCount = max(1, (int) get_user_meta($userId, 'stripe_interval_count', true));

  if ($amount <= 0 || $currency === '' || $interval === '') {
    return '';
  }

  $language = function_exists('dashboard_lang') ? dashboard_lang() : 'de';
  $number = number_format($amount / 100, 2, $language === 'de' ? ',' : '.', $language === 'de' ? '.' : ',');
  $currencyLabel = match ($currency) {
    'eur' => $language === 'de' ? '€' : '€',
    'usd' => '$',
    'gbp' => '£',
    default => strtoupper($currency),
  };

  $intervalLabel = match ($interval) {
    'day' => $language === 'de' ? 'Tag' : 'day',
    'week' => $language === 'de' ? 'Woche' : 'week',
    'year' => $language === 'de' ? 'Jahr' : 'year',
    default => $language === 'de' ? 'Monat' : 'month',
  };

  if ($intervalCount > 1) {
    $intervalLabel = $language === 'de'
      ? $intervalCount . ' ' . match ($interval) {
        'day' => 'Tage',
        'week' => 'Wochen',
        'year' => 'Jahre',
        default => 'Monate',
      }
      : $intervalCount . ' ' . $intervalLabel . 's';
  }

  return $language === 'de'
    ? $number . ' ' . $currencyLabel . ' / ' . $intervalLabel
    : $currencyLabel . $number . ' / ' . $intervalLabel;
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

  dashboard_stripe_sync_subscription_price($userId, $subscription);
  dashboard_stripe_sync_trial_fee_from_metadata($userId, $subscription);

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

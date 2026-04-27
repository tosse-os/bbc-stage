<?php

/**
 * Registriert die Stripe Webhook Route für das Dashboard.
 * Verarbeitet Subscription-Events serverseitig per REST Endpoint.
 */
add_action('rest_api_init', __NAMESPACE__ . '\\register_stripe_webhook_route');

/**
 * Registriert den Stripe Webhook Endpoint.
 * Der Endpoint dient ausschließlich dem Empfang signierter Stripe-Events.
 */
function register_stripe_webhook_route(): void
{
  register_rest_route('bloombridge/v1', '/stripe/webhook', [
    'methods' => 'POST',
    'callback' => __NAMESPACE__ . '\\handle_stripe_webhook_request',
    'permission_callback' => '__return_true',
  ]);
}

/**
 * Liest einen Stripe-Umgebungswert aus der Server-Konfiguration.
 * Nutzt die gesetzten ENV-Werte aus Bedrock.
 */
function stripe_webhook_env(string $key): string
{
  $value = getenv($key);

  return is_string($value) ? trim($value) : '';
}

/**
 * Liefert den Stripe-Client für serverseitige Webhook-Folgeabfragen.
 * Verwendet den Secret Key aus der Umgebung.
 */
function stripe_webhook_client(): \Stripe\StripeClient
{
  return new \Stripe\StripeClient(stripe_webhook_env('STRIPE_SECRET_KEY'));
}

/**
 * Prüft, ob ein Stripe-Event bereits verarbeitet wurde.
 * Verhindert doppelte Verarbeitung identischer Webhook-Events.
 */
function stripe_event_processed(string $event_id): bool
{
  return (bool) get_transient('stripe_event_' . md5($event_id));
}

/**
 * Markiert ein Stripe-Event als verarbeitet.
 * Speichert die Event-ID temporär zur Deduplizierung.
 */
function stripe_mark_event_processed(string $event_id): void
{
  set_transient('stripe_event_' . md5($event_id), 1, WEEK_IN_SECONDS);
}

/**
 * Mappt rohe Stripe-Subscription-Status auf den Dashboard-Zugriffsstatus.
 * Hält die bestehende user_meta-Logik des Dashboards konsistent.
 */
function stripe_map_dashboard_subscription_status(string $stripeStatus): string
{
  if ($stripeStatus === 'active') {
    return 'active';
  }

  if ($stripeStatus === 'trialing') {
    return 'trial';
  }

  return 'payment_required';
}

/**
 * Sucht einen User anhand der Stripe Customer ID.
 * Nutzt das gespeicherte User-Meta als stabile Zuordnung.
 */
function stripe_find_user_by_customer_id(string $customerId): int
{
  if ($customerId === '') {
    return 0;
  }

  $users = get_users([
    'number' => 1,
    'fields' => 'ids',
    'meta_key' => 'stripe_customer_id',
    'meta_value' => $customerId,
  ]);

  return !empty($users[0]) ? (int) $users[0] : 0;
}

/**
 * Ermittelt einen User aus einer Stripe Checkout Session.
 * Nutzt client_reference_id, metadata.user_id, Customer ID oder E-Mail.
 */
function stripe_resolve_user_id_from_checkout_session(object $session): int
{
  $clientReferenceId = isset($session->client_reference_id) ? (int) $session->client_reference_id : 0;

  if ($clientReferenceId > 0) {
    return $clientReferenceId;
  }

  $metadataUserId = isset($session->metadata->user_id) ? (int) $session->metadata->user_id : 0;

  if ($metadataUserId > 0) {
    return $metadataUserId;
  }

  $customerId = isset($session->customer) ? (string) $session->customer : '';

  if ($customerId !== '') {
    $userId = stripe_find_user_by_customer_id($customerId);

    if ($userId > 0) {
      return $userId;
    }
  }

  $email = isset($session->customer_details->email) ? sanitize_email((string) $session->customer_details->email) : '';

  if ($email !== '') {
    $user = get_user_by('email', $email);

    if ($user) {
      return (int) $user->ID;
    }
  }

  return 0;
}

/**
 * Schreibt die Stripe-Metadaten und den Dashboard-Status an den User.
 * Hält Stripe-IDs, Rohstatus und Access-State zentral zusammen.
 */
function stripe_store_user_subscription_state(int $user_id, array $data): void
{
  if ($user_id <= 0) {
    return;
  }

  if (array_key_exists('stripe_customer_id', $data)) {
    update_user_meta($user_id, 'stripe_customer_id', (string) $data['stripe_customer_id']);
  }

  if (array_key_exists('stripe_subscription_id', $data)) {
    update_user_meta($user_id, 'stripe_subscription_id', (string) $data['stripe_subscription_id']);
  }

  if (array_key_exists('stripe_subscription_status_raw', $data)) {
    update_user_meta($user_id, 'stripe_subscription_status_raw', (string) $data['stripe_subscription_status_raw']);
  }

  if (array_key_exists('stripe_current_period_end', $data)) {
    update_user_meta($user_id, 'stripe_current_period_end', (int) $data['stripe_current_period_end']);
  }

  if (array_key_exists('stripe_cancel_at_period_end', $data)) {
    update_user_meta($user_id, 'stripe_cancel_at_period_end', !empty($data['stripe_cancel_at_period_end']) ? '1' : '0');
  }

  if (array_key_exists('stripe_last_event_id', $data)) {
    update_user_meta($user_id, 'stripe_last_event_id', (string) $data['stripe_last_event_id']);
  }

  if (array_key_exists('stripe_last_event_at', $data)) {
    update_user_meta($user_id, 'stripe_last_event_at', (int) $data['stripe_last_event_at']);
  }

  if (array_key_exists('dashboard_subscription_status', $data)) {
    dashboard_set_subscription_state($user_id, (string) $data['dashboard_subscription_status']);
  }
}

/**
 * Verarbeitet abgeschlossene Stripe Checkout Sessions.
 * Verknüpft Customer und Subscription mit dem passenden WordPress-User.
 */
function stripe_handle_checkout_session_completed(object $event): void
{
  $session = $event->data->object ?? null;

  if (!$session || empty($session->subscription)) {
    return;
  }

  $user_id = stripe_resolve_user_id_from_checkout_session($session);

  if ($user_id <= 0) {
    return;
  }

  $subscription = stripe_webhook_client()->subscriptions->retrieve((string) $session->subscription, []);

  $rawStatus = isset($subscription->status) ? (string) $subscription->status : '';

  stripe_store_user_subscription_state($user_id, [
    'stripe_customer_id' => isset($session->customer) ? (string) $session->customer : '',
    'stripe_subscription_id' => isset($subscription->id) ? (string) $subscription->id : '',
    'stripe_subscription_status_raw' => $rawStatus,
    'stripe_current_period_end' => isset($subscription->current_period_end) ? (int) $subscription->current_period_end : 0,
    'stripe_cancel_at_period_end' => !empty($subscription->cancel_at_period_end),
    'stripe_last_event_id' => (string) $event->id,
    'stripe_last_event_at' => time(),
    'dashboard_subscription_status' => stripe_map_dashboard_subscription_status($rawStatus),
  ]);
}

/**
 * Verarbeitet Stripe Subscription-Änderungen.
 * Aktualisiert den gespeicherten Dashboard-Status anhand der Customer ID.
 */
function stripe_handle_subscription_event(object $event): void
{
  $subscription = $event->data->object ?? null;

  if (!$subscription) {
    return;
  }

  $customerId = isset($subscription->customer) ? (string) $subscription->customer : '';
  $user_id = stripe_find_user_by_customer_id($customerId);

  if ($user_id <= 0) {
    return;
  }

  $rawStatus = isset($subscription->status) ? (string) $subscription->status : '';

  stripe_store_user_subscription_state($user_id, [
    'stripe_customer_id' => $customerId,
    'stripe_subscription_id' => isset($subscription->id) ? (string) $subscription->id : '',
    'stripe_subscription_status_raw' => $rawStatus,
    'stripe_current_period_end' => isset($subscription->current_period_end) ? (int) $subscription->current_period_end : 0,
    'stripe_cancel_at_period_end' => !empty($subscription->cancel_at_period_end),
    'stripe_last_event_id' => (string) $event->id,
    'stripe_last_event_at' => time(),
    'dashboard_subscription_status' => stripe_map_dashboard_subscription_status($rawStatus),
  ]);
}

/**
 * Verarbeitet fehlgeschlagene Stripe-Rechnungen.
 * Setzt den Dashboard-Zugriffsstatus des betroffenen Users auf payment_required.
 */
function stripe_handle_invoice_payment_failed(object $event): void
{
  $invoice = $event->data->object ?? null;

  if (!$invoice) {
    return;
  }

  $customerId = isset($invoice->customer) ? (string) $invoice->customer : '';
  $user_id = stripe_find_user_by_customer_id($customerId);

  if ($user_id <= 0) {
    return;
  }

  stripe_store_user_subscription_state($user_id, [
    'stripe_customer_id' => $customerId,
    'stripe_last_event_id' => (string) $event->id,
    'stripe_last_event_at' => time(),
    'dashboard_subscription_status' => 'payment_required',
  ]);
}

/**
 * Verarbeitet eingehende Stripe Webhooks mit Signaturprüfung.
 * Akzeptiert nur valide Stripe-Events und verteilt sie auf die passenden Handler.
 */
function handle_stripe_webhook_request(\WP_REST_Request $request): \WP_REST_Response
{
  $payload = $request->get_body();
  $signature = (string) $request->get_header('stripe-signature');
  $webhookSecret = stripe_webhook_env('STRIPE_WEBHOOK_SECRET');

  if ($webhookSecret === '' || $signature === '') {
    return new \WP_REST_Response([
      'success' => false,
      'code' => 'stripe_webhook_not_configured',
    ], 400);
  }

  try {
    $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
  } catch (\UnexpectedValueException $exception) {
    return new \WP_REST_Response([
      'success' => false,
      'code' => 'stripe_invalid_payload',
    ], 400);
  } catch (\Stripe\Exception\SignatureVerificationException $exception) {
    return new \WP_REST_Response([
      'success' => false,
      'code' => 'stripe_invalid_signature',
    ], 400);
  }

  if (stripe_event_processed((string) $event->id)) {
    return new \WP_REST_Response([
      'success' => true,
      'code' => 'stripe_event_already_processed',
    ], 200);
  }

  if ($event->type === 'checkout.session.completed') {
    stripe_handle_checkout_session_completed($event);
  }

  if (
    $event->type === 'customer.subscription.created' ||
    $event->type === 'customer.subscription.updated' ||
    $event->type === 'customer.subscription.deleted'
  ) {
    stripe_handle_subscription_event($event);
  }

  if ($event->type === 'invoice.payment_failed') {
    stripe_handle_invoice_payment_failed($event);
  }

  stripe_mark_event_processed((string) $event->id);

  return new \WP_REST_Response([
    'success' => true,
    'code' => 'stripe_event_processed',
  ], 200);
}

<?php

/**
 * Registriert die Stripe-Webhook-Route.
 */
function dashboard_register_stripe_webhook_route()
{
  register_rest_route('bloombridge/v1', '/stripe/webhook', [
    'methods' => 'POST',
    'callback' => 'dashboard_handle_stripe_webhook_request',
    'permission_callback' => '__return_true',
  ]);
}

add_action('rest_api_init', 'dashboard_register_stripe_webhook_route');

/**
 * Verarbeitet eingehende Stripe-Webhook-Requests.
 */
function dashboard_handle_stripe_webhook_request($request)
{
  if (!dashboard_stripe_boot()) {
    return new \WP_REST_Response([
      'success' => false,
      'code' => 'stripe_sdk_missing',
    ], 500);
  }

  $configError = dashboard_stripe_webhook_config_error();

  if ($configError !== '') {
    return new \WP_REST_Response([
      'success' => false,
      'code' => $configError,
    ], 500);
  }

  $payload = $request->get_body();
  $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

  try {
    $event = \Stripe\Webhook::constructEvent(
      $payload,
      $signature,
      dashboard_stripe_webhook_secret()
    );
  } catch (\UnexpectedValueException $e) {
    return new \WP_REST_Response([
      'success' => false,
      'code' => 'invalid_payload',
    ], 400);
  } catch (\Stripe\Exception\SignatureVerificationException $e) {
    return new \WP_REST_Response([
      'success' => false,
      'code' => 'invalid_signature',
    ], 400);
  }

  dashboard_process_stripe_event($event);

  return new \WP_REST_Response([
    'success' => true,
  ], 200);
}

/**
 * Leitet Stripe-Events an die passende Verarbeitung weiter.
 */
function dashboard_process_stripe_event($event): void
{
  $type = (string) ($event->type ?? '');
  $object = $event->data->object ?? null;

  if (!$object) {
    return;
  }

  switch ($type) {
    case 'checkout.session.completed':
      dashboard_handle_stripe_checkout_completed($object);
      return;

    case 'customer.subscription.created':
    case 'customer.subscription.updated':
    case 'customer.subscription.deleted':
      dashboard_handle_stripe_subscription_event($object);
      return;

    case 'invoice.payment_succeeded':
      dashboard_handle_stripe_invoice_paid($object);
      return;

    case 'invoice.payment_failed':
      dashboard_handle_stripe_invoice_failed($object);
      return;
  }
}

/**
 * Verarbeitet ein abgeschlossenes Stripe Checkout Event.
 */
function dashboard_handle_stripe_checkout_completed($session): void
{
  $user = dashboard_stripe_find_user_from_checkout_session($session);

  if (!$user) {
    return;
  }

  $customerId = trim((string) ($session->customer ?? ''));

  if ($customerId !== '') {
    dashboard_stripe_store_customer_id($user->ID, $customerId);
  }

  $subscriptionId = trim((string) ($session->subscription ?? ''));

  if ($subscriptionId === '') {
    return;
  }

  try {
    $subscription = \Stripe\Subscription::retrieve($subscriptionId);
    dashboard_stripe_sync_subscription($user->ID, $subscription);
  } catch (\Throwable $e) {
  }
}

/**
 * Verarbeitet Stripe Subscription Events.
 */
function dashboard_handle_stripe_subscription_event($subscription): void
{
  $user = dashboard_stripe_find_user_from_subscription($subscription);

  if (!$user) {
    return;
  }

  dashboard_stripe_sync_subscription($user->ID, $subscription);
}

/**
 * Verarbeitet erfolgreiche Stripe-Rechnungen.
 */
function dashboard_handle_stripe_invoice_paid($invoice): void
{
  $user = dashboard_stripe_find_user_from_invoice($invoice);

  if (!$user) {
    return;
  }

  $customerId = trim((string) ($invoice->customer ?? ''));

  if ($customerId !== '') {
    dashboard_stripe_store_customer_id($user->ID, $customerId);
  }

  $subscriptionId = trim((string) ($invoice->subscription ?? ''));

  if ($subscriptionId === '') {
    dashboard_set_subscription_state($user->ID, 'active');
    update_user_meta($user->ID, 'stripe_subscription_status', 'active');
    return;
  }

  try {
    $subscription = \Stripe\Subscription::retrieve($subscriptionId);
    dashboard_stripe_sync_subscription($user->ID, $subscription);
  } catch (\Throwable $e) {
    dashboard_set_subscription_state($user->ID, 'active');
    update_user_meta($user->ID, 'stripe_subscription_status', 'active');
  }
}

/**
 * Verarbeitet fehlgeschlagene Stripe-Rechnungen.
 */
function dashboard_handle_stripe_invoice_failed($invoice): void
{
  $user = dashboard_stripe_find_user_from_invoice($invoice);

  if (!$user) {
    return;
  }

  $customerId = trim((string) ($invoice->customer ?? ''));

  if ($customerId !== '') {
    dashboard_stripe_store_customer_id($user->ID, $customerId);
  }

  $subscriptionId = trim((string) ($invoice->subscription ?? ''));

  if ($subscriptionId !== '') {
    try {
      $subscription = \Stripe\Subscription::retrieve($subscriptionId);
      dashboard_stripe_sync_subscription($user->ID, $subscription);
      return;
    } catch (\Throwable $e) {
    }
  }

  dashboard_set_subscription_state($user->ID, 'past_due');
  update_user_meta($user->ID, 'stripe_subscription_status', 'past_due');
}

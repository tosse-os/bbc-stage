<?php

if (!function_exists('dashboard_stripe_find_user_id_by_customer')) {
  function dashboard_stripe_find_user_id_by_customer(string $customer_id): int
  {
    if ($customer_id === '') {
      return 0;
    }

    $users = get_users([
      'fields' => 'ids',
      'number' => 1,
      'meta_key' => 'stripe_customer_id',
      'meta_value' => $customer_id,
    ]);

    return !empty($users) ? (int) $users[0] : 0;
  }
}

if (!function_exists('dashboard_stripe_find_user_id_by_email')) {
  function dashboard_stripe_find_user_id_by_email(string $email): int
  {
    if ($email === '') {
      return 0;
    }

    $user = get_user_by('email', $email);

    return $user ? (int) $user->ID : 0;
  }
}

if (!function_exists('dashboard_stripe_find_user_id_from_subscription')) {
  function dashboard_stripe_find_user_id_from_subscription($subscription): int
  {
    if (!empty($subscription->metadata) && !empty($subscription->metadata->user_id)) {
      return (int) $subscription->metadata->user_id;
    }

    if (!empty($subscription->customer)) {
      $user_id = dashboard_stripe_find_user_id_by_customer((string) $subscription->customer);
      if ($user_id > 0) {
        return $user_id;
      }
    }

    return 0;
  }
}

if (!function_exists('dashboard_stripe_map_state')) {
  function dashboard_stripe_map_state(string $stripe_status): string
  {
    return match ($stripe_status) {
      'active', 'trialing' => 'active',
      'past_due', 'unpaid', 'incomplete', 'incomplete_expired' => 'past_due',
      'canceled' => 'canceled',
      default => 'payment_required',
    };
  }
}

if (!function_exists('dashboard_stripe_store_subscription')) {
  function dashboard_stripe_store_subscription(int $user_id, $subscription): void
  {
    if ($user_id <= 0) {
      return;
    }

    if (!empty($subscription->customer)) {
      update_user_meta($user_id, 'stripe_customer_id', (string) $subscription->customer);
    }

    if (!empty($subscription->id)) {
      update_user_meta($user_id, 'stripe_subscription_id', (string) $subscription->id);
    }

    dashboard_set_subscription_state(
      $user_id,
      dashboard_stripe_map_state((string) $subscription->status)
    );
  }
}

add_action('rest_api_init', function () {
  register_rest_route('bloombridge/v1', '/stripe/webhook', [
    'methods' => 'POST',
    'callback' => 'dashboard_stripe_handle_webhook',
    'permission_callback' => '__return_true',
  ]);
});

function dashboard_stripe_handle_webhook(\WP_REST_Request $request)
{
  if (!dashboard_stripe_boot_sdk()) {
    return new \WP_REST_Response(['ok' => false, 'error' => 'stripe_sdk_missing'], 500);
  }

  $config = dashboard_stripe_config();

  if ($config['secret_key'] === '' || $config['webhook_secret'] === '') {
    return new \WP_REST_Response(['ok' => false, 'error' => 'stripe_not_configured'], 500);
  }

  $payload = $request->get_body();
  $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

  try {
    $event = \Stripe\Webhook::constructEvent(
      $payload,
      $signature,
      $config['webhook_secret']
    );
  } catch (\UnexpectedValueException $e) {
    return new \WP_REST_Response(['ok' => false, 'error' => 'invalid_payload'], 400);
  } catch (\Stripe\Exception\SignatureVerificationException $e) {
    return new \WP_REST_Response(['ok' => false, 'error' => 'invalid_signature'], 400);
  }

  switch ($event->type) {
    case 'checkout.session.completed':
      $session = $event->data->object;

      $user_id = 0;

      if (!empty($session->client_reference_id)) {
        $user_id = (int) $session->client_reference_id;
      }

      if ($user_id <= 0 && !empty($session->customer)) {
        $user_id = dashboard_stripe_find_user_id_by_customer((string) $session->customer);
      }

      if ($user_id <= 0 && !empty($session->customer_details) && !empty($session->customer_details->email)) {
        $user_id = dashboard_stripe_find_user_id_by_email((string) $session->customer_details->email);
      }

      if ($user_id > 0) {
        if (!empty($session->customer)) {
          update_user_meta($user_id, 'stripe_customer_id', (string) $session->customer);
        }

        if (!empty($session->subscription)) {
          update_user_meta($user_id, 'stripe_subscription_id', (string) $session->subscription);
        }

        dashboard_set_subscription_state($user_id, 'active');
      }
      break;

    case 'customer.subscription.created':
    case 'customer.subscription.updated':
    case 'customer.subscription.deleted':
      $subscription = $event->data->object;
      $user_id = dashboard_stripe_find_user_id_from_subscription($subscription);
      dashboard_stripe_store_subscription($user_id, $subscription);
      break;

    case 'invoice.paid':
      $invoice = $event->data->object;
      $user_id = !empty($invoice->customer)
        ? dashboard_stripe_find_user_id_by_customer((string) $invoice->customer)
        : 0;

      if ($user_id > 0) {
        dashboard_set_subscription_state($user_id, 'active');

        if (!empty($invoice->subscription)) {
          update_user_meta($user_id, 'stripe_subscription_id', (string) $invoice->subscription);
        }
      }
      break;

    case 'invoice.payment_failed':
      $invoice = $event->data->object;
      $user_id = !empty($invoice->customer)
        ? dashboard_stripe_find_user_id_by_customer((string) $invoice->customer)
        : 0;

      if ($user_id > 0) {
        dashboard_set_subscription_state($user_id, 'past_due');

        if (!empty($invoice->subscription)) {
          update_user_meta($user_id, 'stripe_subscription_id', (string) $invoice->subscription);
        }
      }
      break;
  }

  return new \WP_REST_Response(['received' => true], 200);
}

<?php

function dashboard_set_subscription_state(int $user_id, string $state): void
{
  $allowed = ['trial', 'active', 'payment_required', 'past_due', 'canceled'];

  if (!in_array($state, $allowed, true)) {
    return;
  }

  update_user_meta($user_id, USER_META_SUB_STATUS, $state);

  if ($state === 'active') {
    delete_user_meta($user_id, USER_META_TRIAL_END);
  }
}

<?php

/**
 * Setzt den Subscription-Zustand eines Users
 * Zentrale Schreibstelle für Trial / Active / Payment Required
 */
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

/**
 * Fügt im WordPress-Admin im Benutzerprofil ein Feld zur Subscription-Steuerung hinzu
 */
add_action('show_user_profile', 'dashboard_subscription_admin_field');
add_action('edit_user_profile', 'dashboard_subscription_admin_field');

function dashboard_subscription_admin_field($user)
{
  if (!current_user_can('manage_options')) {
    return;
  }

  $state = get_user_meta($user->ID, USER_META_SUB_STATUS, true);
?>
  <h2>Dashboard Subscription</h2>

  <table class="form-table">
    <tr>
      <th><label for="dashboard_subscription_status">Subscription-Status</label></th>
      <td>
        <select name="dashboard_subscription_status" id="dashboard_subscription_status">
          <option value="trial" <?php selected($state, 'trial'); ?>>Trial</option>
          <option value="active" <?php selected($state, 'active'); ?>>Active (Paid)</option>
          <option value="payment_required" <?php selected($state, 'payment_required'); ?>>Payment required</option>
          <option value="past_due" <?php selected($state, 'past_due'); ?>>Past due</option>
          <option value="canceled" <?php selected($state, 'canceled'); ?>>Canceled</option>
        </select>
      </td>
    </tr>
  </table>
<?php
}

/**
 * Speichert den im Admin gesetzten Subscription-Status
 */
add_action('personal_options_update', 'dashboard_subscription_admin_save');
add_action('edit_user_profile_update', 'dashboard_subscription_admin_save');

function dashboard_subscription_admin_save($user_id)
{
  if (!current_user_can('manage_options')) {
    return;
  }

  if (!isset($_POST['dashboard_subscription_status'])) {
    return;
  }

  dashboard_set_subscription_state(
    $user_id,
    sanitize_text_field($_POST['dashboard_subscription_status'])
  );
}

function dashboard_admin_user_subscription_plan_label(int $userId): string
{
  $plan = function_exists('dashboard_stripe_user_current_plan')
    ? dashboard_stripe_user_current_plan($userId)
    : '';

  if ($plan === '') {
    $selectedPlan = function_exists('dashboard_stripe_normalize_plan')
      ? dashboard_stripe_normalize_plan(get_user_meta($userId, 'dashboard_selected_plan', true))
      : sanitize_key((string) get_user_meta($userId, 'dashboard_selected_plan', true));

    $plan = in_array($selectedPlan, ['trial', 'basis', 'pro'], true) ? $selectedPlan : '';
  }

  return match ($plan) {
    'trial' => 'Trial',
    'basis' => 'Basis',
    'pro' => 'Pro',
    default => '—',
  };
}

function dashboard_admin_user_subscription_state_label(int $userId): string
{
  $state = (string) get_user_meta($userId, USER_META_SUB_STATUS, true);
  $isDisplayedTrial = function_exists('dashboard_stripe_user_should_display_trial')
    ? dashboard_stripe_user_should_display_trial($userId)
    : false;

  if ($isDisplayedTrial) {
    return 'Testphase';
  }

  return match ($state) {
    'active' => 'Aktiv',
    'trial' => 'Testphase',
    'payment_required' => 'Zahlung erforderlich',
    'past_due' => 'Zahlung überfällig',
    'canceled' => 'Gekündigt',
    default => $state !== '' ? ucfirst(str_replace('_', ' ', $state)) : '—',
  };
}

function dashboard_admin_user_subscription_badge_class(int $userId): string
{
  $state = (string) get_user_meta($userId, USER_META_SUB_STATUS, true);
  $isDisplayedTrial = function_exists('dashboard_stripe_user_should_display_trial')
    ? dashboard_stripe_user_should_display_trial($userId)
    : false;

  if ($isDisplayedTrial) {
    return 'dashboard-subscription-badge dashboard-subscription-badge-trial';
  }

  return match ($state) {
    'active' => 'dashboard-subscription-badge dashboard-subscription-badge-active',
    'past_due' => 'dashboard-subscription-badge dashboard-subscription-badge-warning',
    'payment_required' => 'dashboard-subscription-badge dashboard-subscription-badge-warning',
    'canceled' => 'dashboard-subscription-badge dashboard-subscription-badge-muted',
    default => 'dashboard-subscription-badge dashboard-subscription-badge-muted',
  };
}

function dashboard_admin_format_stripe_id(string $value): string
{
  $value = trim($value);

  if ($value === '') {
    return '—';
  }

  if (strlen($value) <= 18) {
    return $value;
  }

  return substr($value, 0, 10) . '…' . substr($value, -6);
}

add_filter('manage_users_columns', function ($columns) {
  $newColumns = [];

  foreach ($columns as $key => $label) {
    $newColumns[$key] = $label;

    if ($key === 'email') {
      $newColumns['dashboard_subscription'] = 'Dashboard Abo';
      $newColumns['dashboard_stripe'] = 'Stripe';
    }
  }

  return $newColumns;
});

add_filter('manage_users_custom_column', function ($output, $columnName, $userId) {
  $userId = (int) $userId;

  if ($columnName === 'dashboard_subscription') {
    $stateLabel = dashboard_admin_user_subscription_state_label($userId);
    $planLabel = dashboard_admin_user_subscription_plan_label($userId);
    $badgeClass = dashboard_admin_user_subscription_badge_class($userId);

    $currentPeriodEnd = (int) get_user_meta($userId, 'stripe_current_period_end', true);
    $stripeTrialStart = (int) get_user_meta($userId, 'stripe_trial_start', true);
    $stripeTrialEnd = (int) get_user_meta($userId, 'stripe_trial_end', true);
    $trialUsedAt = (string) get_user_meta($userId, 'dashboard_trial_used_at', true);
    $isTrialDisplay = function_exists('dashboard_stripe_user_should_display_trial')
      ? dashboard_stripe_user_should_display_trial($userId)
      : $planLabel === 'Trial';

    $displayPeriodEnd = $isTrialDisplay && $stripeTrialEnd > 0
      ? $stripeTrialEnd
      : $currentPeriodEnd;

    $html = '<div class="dashboard-subscription-admin">';
    $html .= '<span class="' . esc_attr($badgeClass) . '">' . esc_html($stateLabel) . '</span>';
    $html .= '<div><strong>Plan:</strong> ' . esc_html($planLabel) . '</div>';

    if ($isTrialDisplay && $stripeTrialStart > 0) {
      $html .= '<div><strong>Trial Start:</strong> ' . esc_html(date_i18n('d.m.Y', $stripeTrialStart)) . '</div>';
    }

    if ($displayPeriodEnd > 0) {
      $label = $isTrialDisplay ? 'Trial bis' : 'Bis';
      $html .= '<div><strong>' . esc_html($label) . ':</strong> ' . esc_html(date_i18n('d.m.Y', $displayPeriodEnd)) . '</div>';
    }

    if ($trialUsedAt !== '') {
      $html .= '<div><strong>Trial genutzt:</strong> ja</div>';
    }

    $html .= '</div>';

    return $html;
  }

  if ($columnName === 'dashboard_stripe') {
    $stripeStatus = (string) get_user_meta($userId, 'stripe_subscription_status', true);
    $customerId = (string) get_user_meta($userId, 'stripe_customer_id', true);
    $subscriptionId = (string) get_user_meta($userId, 'stripe_subscription_id', true);

    $html = '<div class="dashboard-stripe-admin">';
    $html .= '<div><strong>Status:</strong> ' . esc_html($stripeStatus !== '' ? $stripeStatus : '—') . '</div>';
    $html .= '<div><strong>Customer:</strong> <code>' . esc_html(dashboard_admin_format_stripe_id($customerId)) . '</code></div>';
    $html .= '<div><strong>Sub:</strong> <code>' . esc_html(dashboard_admin_format_stripe_id($subscriptionId)) . '</code></div>';
    $html .= '</div>';

    return $html;
  }

  return $output;
}, 10, 3);

add_filter('manage_users_sortable_columns', function ($columns) {
  $columns['dashboard_subscription'] = 'dashboard_subscription_status';
  return $columns;
});

add_action('pre_get_users', function ($query) {
  if (!is_admin()) {
    return;
  }

  if (($query->get('orderby') ?? '') !== 'dashboard_subscription_status') {
    return;
  }

  $query->set('meta_key', USER_META_SUB_STATUS);
  $query->set('orderby', 'meta_value');
});

add_action('admin_head-users.php', function () {
  echo '<style>
    .column-dashboard_subscription {
      width: 210px;
    }

    .column-dashboard_stripe {
      width: 260px;
    }

    .dashboard-subscription-admin,
    .dashboard-stripe-admin {
      line-height: 1.55;
      font-size: 12px;
    }

    .dashboard-subscription-admin code,
    .dashboard-stripe-admin code {
      font-size: 11px;
    }

    .dashboard-subscription-badge {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      padding: 2px 8px;
      margin-bottom: 4px;
      font-size: 11px;
      font-weight: 600;
      line-height: 1.5;
    }

    .dashboard-subscription-badge-active {
      background: #dcfce7;
      color: #166534;
    }

    .dashboard-subscription-badge-trial {
      background: #dbeafe;
      color: #1d4ed8;
    }

    .dashboard-subscription-badge-warning {
      background: #fef3c7;
      color: #92400e;
    }

    .dashboard-subscription-badge-muted {
      background: #e5e7eb;
      color: #374151;
    }
  </style>';
});

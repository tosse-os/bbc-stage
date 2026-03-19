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

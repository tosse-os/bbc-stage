<?php

/**
 * Registrierung & Trial-Initialisierung
 * Setzt Trial-Zeitraum, Subscription-Status und Default-Theme.
 */

const USER_META_TRIAL_START = 'trial_started_at';
const USER_META_TRIAL_END   = 'trial_ends_at';
const USER_META_SUB_STATUS  = 'subscription_status';
const USER_META_THEME       = 'dashboard_theme';

add_action('user_register', function ($user_id) {
  $now = current_time('timestamp');

  update_user_meta($user_id, USER_META_TRIAL_START, $now);
  update_user_meta($user_id, USER_META_TRIAL_END, strtotime('+5 days', $now));
  update_user_meta($user_id, USER_META_SUB_STATUS, 'trial');
  update_user_meta($user_id, USER_META_THEME, 'light');
});



add_action('admin_post_nopriv_dashboard_register', function () {
  $user_id = wp_create_user(
    sanitize_email($_POST['email']),
    $_POST['password'],
    sanitize_email($_POST['email'])
  );

  wp_update_user([
    'ID' => $user_id,
    'display_name' => sanitize_text_field($_POST['name']),
  ]);

  wp_set_current_user($user_id);
  wp_set_auth_cookie($user_id);

  wp_redirect('/dashboard');
  exit;
});

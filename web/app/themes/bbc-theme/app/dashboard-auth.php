<?php

const USER_META_TRIAL_START = 'trial_started_at';
const USER_META_TRIAL_END   = 'trial_ends_at';
const USER_META_SUB_STATUS  = 'subscription_status';
const USER_META_THEME       = 'dashboard_theme';

function dashboard_user_should_use_wp_admin($user): bool
{
  if (!$user || is_wp_error($user)) {
    return false;
  }

  $roles = (array) $user->roles;

  if (in_array('subscriber', $roles, true) || in_array('abonnent', $roles, true)) {
    return false;
  }

  return !empty($roles);
}

add_action('user_register', function ($user_id) {
  $now = current_time('timestamp');

  update_user_meta($user_id, USER_META_TRIAL_START, $now);
  update_user_meta($user_id, USER_META_TRIAL_END, strtotime('+5 days', $now));
  update_user_meta($user_id, USER_META_SUB_STATUS, 'trial');
  update_user_meta($user_id, USER_META_THEME, 'light');
});

add_action('admin_post_nopriv_dashboard_login', function () {
  $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $key = 'login_attempts_' . md5($ip);
  $attempts = (int) get_transient($key);

  if ($attempts >= 5) {
    wp_safe_redirect('/dashboard-login?error=too_many_attempts');
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_login')
  ) {
    wp_safe_redirect('/dashboard-login?error=invalid_request');
    exit;
  }

  $creds = [
    'user_login'    => sanitize_email($_POST['email'] ?? ''),
    'user_password' => $_POST['password'] ?? '',
    'remember'      => true,
  ];

  $user = wp_signon($creds, false);

  if (is_wp_error($user)) {
    set_transient($key, $attempts + 1, 15 * MINUTE_IN_SECONDS);
    wp_safe_redirect('/dashboard-login?error=1');
    exit;
  }

  delete_transient($key);

  if (dashboard_user_should_use_wp_admin($user)) {
    wp_safe_redirect(admin_url());
    exit;
  }

  wp_safe_redirect('/dashboard');
  exit;
});

add_action('admin_post_nopriv_dashboard_register', function () {

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_register')
  ) {
    wp_safe_redirect('/dashboard-register?error=invalid_request');
    exit;
  }

  $email = sanitize_email($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $name = sanitize_text_field($_POST['name'] ?? '');

  if (strlen($password) < 8) {
    wp_safe_redirect('/dashboard-register?error=weak_password');
    exit;
  }

  if (!is_email($email)) {
    wp_safe_redirect('/dashboard-register?error=email');
    exit;
  }

  if (email_exists($email)) {
    wp_safe_redirect('/dashboard-register?error=exists');
    exit;
  }

  $user_id = wp_create_user($email, $password, $email);

  if (is_wp_error($user_id)) {
    wp_safe_redirect('/dashboard-register?error=create_failed');
    exit;
  }

  wp_update_user([
    'ID' => $user_id,
    'display_name' => $name,
  ]);

  wp_set_current_user($user_id);
  wp_set_auth_cookie($user_id);

  $user = get_user_by('id', $user_id);

  if (dashboard_user_should_use_wp_admin($user)) {
    wp_safe_redirect(admin_url());
    exit;
  }

  wp_safe_redirect('/dashboard');
  exit;
});

add_action('wp_logout', function () {
  wp_safe_redirect('/dashboard-login');
  exit;
});

add_action('admin_post_dashboard_update_account', function () {

  if (!is_user_logged_in()) {
    wp_safe_redirect('/dashboard-login');
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_update_account')
  ) {
    wp_safe_redirect('/dashboard-settings?tab=account&error=invalid_request');
    exit;
  }

  $user = wp_get_current_user();

  $display_name = sanitize_text_field($_POST['display_name'] ?? '');
  $phone = sanitize_text_field($_POST['phone'] ?? '');

  if ($display_name !== '') {
    wp_update_user([
      'ID' => $user->ID,
      'display_name' => $display_name,
    ]);
  }

  update_user_meta($user->ID, 'phone_number', $phone);

  wp_safe_redirect('/dashboard-settings?tab=account&success=1');
  exit;
});

add_action('admin_post_dashboard_update_password', function () {

  if (!is_user_logged_in()) {
    wp_safe_redirect('/dashboard-login');
    exit;
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_update_password')
  ) {
    wp_safe_redirect('/dashboard-settings?tab=password&error=invalid_request');
    exit;
  }

  $user = wp_get_current_user();

  $current = $_POST['current_password'] ?? '';
  $new = $_POST['new_password'] ?? '';
  $confirm = $_POST['new_password_confirm'] ?? '';

  if (!wp_check_password($current, $user->user_pass, $user->ID)) {
    wp_safe_redirect('/dashboard-settings?tab=password&error=wrong_password');
    exit;
  }

  if ($new === '' || $new !== $confirm) {
    wp_safe_redirect('/dashboard-settings?tab=password&error=mismatch');
    exit;
  }

  if (strlen($new) < 8) {
    wp_safe_redirect('/dashboard-settings?tab=password&error=weak_password');
    exit;
  }

  wp_set_password($new, $user->ID);
  wp_set_auth_cookie($user->ID);

  wp_safe_redirect('/dashboard-settings?tab=password&success=1');
  exit;
});

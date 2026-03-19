<?php

/**
 * User-Meta Konstanten
 */

const USER_META_TRIAL_START = 'trial_started_at';
const USER_META_TRIAL_END   = 'trial_ends_at';
const USER_META_SUB_STATUS  = 'subscription_status';
const USER_META_THEME       = 'dashboard_theme';

/**
 * Initialisiert Trial beim Registrieren
 */

add_action('user_register', function ($user_id) {
  $now = current_time('timestamp');

  update_user_meta($user_id, USER_META_TRIAL_START, $now);
  update_user_meta($user_id, USER_META_TRIAL_END, strtotime('+5 days', $now));
  update_user_meta($user_id, USER_META_SUB_STATUS, 'trial');
  update_user_meta($user_id, USER_META_THEME, 'light');
});

/**
 * Dashboard Login
 */

add_action('admin_post_nopriv_dashboard_login', function () {

  $creds = [
    'user_login'    => sanitize_email($_POST['email']),
    'user_password' => $_POST['password'],
    'remember'      => true,
  ];

  $user = wp_signon($creds, false);

  if (is_wp_error($user)) {
    wp_redirect('/dashboard-login?error=1');
    exit;
  }

  wp_redirect('/dashboard');
  exit;
});

/**
 * Dashboard Registrierung
 */

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

/**
 * Erzwingt sauberen Logout-Redirect
 */

add_action('wp_logout', function () {
  wp_redirect('/dashboard-login');
  exit;
});


/**
 * Update Account
 */
add_action('admin_post_dashboard_update_account', function () {
  if (!is_user_logged_in()) {
    wp_redirect('/dashboard-login');
    exit;
  }

  $user_id = get_current_user_id();

  // 1. Basis-Daten updaten
  $display_name = sanitize_text_field($_POST['display_name'] ?? '');
  $phone = sanitize_text_field($_POST['phone'] ?? '');

  wp_update_user([
    'ID' => $user_id,
    'display_name' => $display_name,
  ]);
  update_user_meta($user_id, 'phone_number', $phone);

  // 2. Avatar Upload Logik
  if (!empty($_FILES['profile_avatar']['name'])) {
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    // Den Upload an die WordPress Mediathek übergeben
    $attachment_id = media_handle_upload('profile_avatar', 0); // 0 = keinem Post zugeordnet

    if (!is_wp_error($attachment_id)) {
      // Die alte ID löschen (optional, um die Mediathek sauber zu halten)
      $old_avatar_id = get_user_meta($user_id, 'profile_avatar_id', true);
      if ($old_avatar_id) {
        wp_delete_attachment($old_avatar_id, true);
      }

      // Neue ID in den User Meta speichern
      update_user_meta($user_id, 'profile_avatar_id', $attachment_id);
    }
  }

  wp_redirect('/dashboard-settings?tab=account&updated=1');
  exit;
});

/**
 * Update PASSWORD
 */
add_action('admin_post_dashboard_update_password', function () {

  if (!is_user_logged_in()) {
    wp_redirect('/dashboard-login');
    exit;
  }

  $user = wp_get_current_user();

  $current = $_POST['current_password'] ?? '';
  $new = $_POST['new_password'] ?? '';
  $confirm = $_POST['new_password_confirm'] ?? '';

  if (!wp_check_password($current, $user->user_pass, $user->ID)) {
    wp_redirect('/dashboard-settings?tab=password&error=wrong_password');
    exit;
  }

  if ($new === '' || $new !== $confirm) {
    wp_redirect('/dashboard-settings?tab=password&error=mismatch');
    exit;
  }

  wp_set_password($new, $user->ID);
  wp_set_auth_cookie($user->ID);

  wp_redirect('/dashboard-settings?tab=password&success=1');
  exit;
});

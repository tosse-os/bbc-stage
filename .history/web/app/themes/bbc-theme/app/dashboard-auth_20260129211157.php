<?php

/**
 * User-Meta-Keys für das Dashboard
 */

const USER_META_TRIAL_START = 'trial_started_at';
const USER_META_TRIAL_END   = 'trial_ends_at';
const USER_META_SUB_STATUS  = 'subscription_status';
const USER_META_THEME       = 'dashboard_theme';


/**
 * Initialisiert Trial-Daten direkt nach Benutzerregistrierung
 *
 * Wird automatisch von WordPress ausgelöst, sobald ein User erstellt wird.
 */
add_action('user_register', function ($user_id) {

  $now = current_time('timestamp');

  update_user_meta($user_id, USER_META_TRIAL_START, $now);
  update_user_meta($user_id, USER_META_TRIAL_END, strtotime('+5 days', $now));
  update_user_meta($user_id, USER_META_SUB_STATUS, 'trial');
  update_user_meta($user_id, USER_META_THEME, 'light');
});


/**
 * Eigene Dashboard-Registrierung
 *
 * Wird über admin-post.php?action=dashboard_register aufgerufen.
 * - Erstellt einen neuen Benutzer
 * - Trial wird automatisch gesetzt (user_register Hook)
 * - User wird sofort eingeloggt
 * - Redirect ins Dashboard
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
 * Erzwingt sauberen Redirect nach Logout
 *
 * Egal von wo ausgeloggt wird → immer zurück zum Dashboard-Login
 */
add_action('wp_logout', function () {
  wp_redirect('/dashboard-login');
  exit;
});

/**
 * Erzwingt Redirect nach erfolgreichem Login ins Dashboard
 * Greift global für alle Logins
 */
add_filter('login_redirect', function ($redirect_to, $requested_redirect_to, $user) {

  if (isset($user->ID)) {
    return '/dashboard';
  }

  return $redirect_to;
}, 10, 3);

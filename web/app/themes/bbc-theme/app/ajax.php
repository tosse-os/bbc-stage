<?php

/**
 * Dashboard AJAX Endpoints
 */

/*
|--------------------------------------------------------------------------
| Theme (Light / Dark)
|--------------------------------------------------------------------------
*/

add_action('wp_ajax_set_dashboard_theme', function () {

  if (!is_user_logged_in()) {
    wp_send_json_error();
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_theme_toggle')
  ) {
    wp_send_json_error();
  }

  $theme = ($_POST['theme'] ?? '') === 'dark' ? 'dark' : 'light';

  update_user_meta(
    get_current_user_id(),
    'dashboard_theme',
    $theme
  );

  wp_send_json_success();
});


/*
|--------------------------------------------------------------------------
| Sidebar Collapse State
|--------------------------------------------------------------------------
*/

add_action('wp_ajax_set_dashboard_sidebar', function () {

  if (!is_user_logged_in()) {
    wp_send_json_error();
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_sidebar_toggle')
  ) {
    wp_send_json_error();
  }

  $collapsed = ($_POST['collapsed'] ?? '') === '1' ? '1' : '0';

  update_user_meta(
    get_current_user_id(),
    'dashboard_sidebar_collapsed',
    $collapsed
  );

  wp_send_json_success();
});


/*
|--------------------------------------------------------------------------
| Kontaktformular
|--------------------------------------------------------------------------
*/

add_action('wp_ajax_contact_form_submit', __NAMESPACE__ . '\\contact_form_submit');
add_action('wp_ajax_nopriv_contact_form_submit', __NAMESPACE__ . '\\contact_form_submit');

function contact_form_submit()
{
  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'contact_form')
  ) {
    wp_send_json_error();
  }

  $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
  $key = 'contact_form_' . md5($ip);

  if (get_transient($key)) {
    wp_send_json_error(['message' => 'Rate limit']);
  }

  set_transient($key, 1, 60);

  $email = trim($_POST['email'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if (!$email || !\is_email($email)) {
    \wp_send_json_error(['field' => 'email']);
  }

  if (!$message || \mb_strlen($message) < 5) {
    \wp_send_json_error(['field' => 'message']);
  }

  \wp_mail(
    \get_option('admin_email'),
    'Kontaktanfrage',
    "E-Mail: {$email}\n\n{$message}"
  );

  \wp_send_json_success();
}

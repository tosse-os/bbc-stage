<?php

namespace App;

\error_log('AJAX FILE LOADED');



/**
 * AJAX Endpoints für das Dashboard
 * Aktuell: Dark / Light Mode Speicherung im User-Meta.
 */

add_action('wp_ajax_set_dashboard_theme', function () {
  if (!is_user_logged_in()) {
    wp_send_json_error();
  }

  $theme = $_POST['theme'] === 'dark' ? 'dark' : 'light';
  update_user_meta(get_current_user_id(), USER_META_THEME, $theme);

  wp_send_json_success();
});


/**
 * AJAX-Handler für das Kontaktformular.
 * Validiert E-Mail und Nachricht serverseitig und versendet die Anfrage an den Admin.
 */

add_action('wp_ajax_contact_form_submit', __NAMESPACE__ . '\\contact_form_submit');
add_action('wp_ajax_nopriv_contact_form_submit', __NAMESPACE__ . '\\contact_form_submit');

function contact_form_submit()
{

  $email = trim($_POST['email'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if (!$email || !\is_email($email)) {
    \wp_send_json_error([
      'field' => 'email'
    ]);
  }

  if (!$message || \mb_strlen($message) < 5) {
    \wp_send_json_error([
      'field' => 'message'
    ]);
  }

  \wp_mail(
    \get_option('admin_email'),
    'Kontaktanfrage',
    "E-Mail: {$email}\n\n{$message}"
  );

  \wp_send_json_success();
}

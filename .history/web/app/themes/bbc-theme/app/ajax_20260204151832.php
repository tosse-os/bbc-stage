<?php

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
 * Validiert Eingaben und versendet die Anfrage per Mail.
 * Antwort erfolgt als JSON.
 */

add_action('wp_ajax_contact_form_submit', 'contact_form_submit');
add_action('wp_ajax_nopriv_contact_form_submit', 'contact_form_submit');

function contact_form_submit()
{
  $email = isset($_POST['email']) ? trim($_POST['email']) : '';
  $message = isset($_POST['message']) ? trim($_POST['message']) : '';

  if (!$email || !is_email($email)) {
    wp_send_json_error(['field' => 'email']);
  }

  if (!$message || mb_strlen($message) < 10) {
    wp_send_json_error(['field' => 'message']);
  }

  wp_mail(
    get_option('admin_email'),
    'Kontaktanfrage',
    "E-Mail: {$email}\n\n{$message}"
  );

  wp_send_json_success();
}

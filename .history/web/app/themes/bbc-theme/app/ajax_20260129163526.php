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

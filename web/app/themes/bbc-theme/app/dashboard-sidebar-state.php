<?php

add_action('wp_ajax_set_dashboard_sidebar_state', function () {
  if (!is_user_logged_in()) {
    wp_send_json_error();
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_sidebar_state')
  ) {
    wp_send_json_error();
  }

  $state = $_POST['state'] === 'collapsed' ? 'collapsed' : 'expanded';
  update_user_meta(get_current_user_id(), 'dashboard_sidebar_state', $state);

  wp_send_json_success();
});

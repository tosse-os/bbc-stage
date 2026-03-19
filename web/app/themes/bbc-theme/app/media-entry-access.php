<?php

function can_view_media_entry($user_id)
{
  $state = dashboard_access_state($user_id);
  return in_array($state, ['trial', 'active'], true);
}

add_action('template_redirect', function () {

  if (get_post_type() === 'media_entry') {
    if (!can_view_media_entry(get_current_user_id())) {
      wp_redirect('/dashboard-payment-required');
      exit;
    }
  }
});

<?php

function can_view_media_entry($user_id)
{
  $state = dashboard_access_state($user_id);
  return in_array($state, ['trial', 'active'], true);
}

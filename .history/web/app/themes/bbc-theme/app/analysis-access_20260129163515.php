<?php

/**
 * Analyse-Zugriffskontrolle
 * Prüft, ob ein User Analysen sehen darf.
 */

function can_view_analysis($user_id)
{
  $state = dashboard_access_state($user_id);
  return in_array($state, ['trial', 'active'], true);
}

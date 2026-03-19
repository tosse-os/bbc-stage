<?php

/**
 * Prüft, ob ein Dashboard-Navigationspunkt aktiv ist
 */
function dashboard_active(string $path): bool
{
  $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
  return str_starts_with($current, trim($path, '/'));
}

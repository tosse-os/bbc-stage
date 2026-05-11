<?php

/**
 * Prüft, ob ein Dashboard-Navigationspunkt aktiv ist
 */
function dashboard_active(string $path, bool $exact = false): bool
{
  $current = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
  $path = trim($path, '/');

  if ($exact) {
    return $current === $path;
  }

  return $current === $path || str_starts_with($current, $path . '/');
}

function dashboard_allowed_inline_html(): array
{
  return [
    'br' => [],
    'strong' => [],
    'b' => [],
    'em' => [],
    'i' => [],
    'p' => [],
    'ul' => [],
    'ol' => [],
    'li' => [],
    'a' => [
      'href' => [],
      'target' => [],
      'rel' => [],
      'title' => [],
    ],
  ];
}

function dashboard_kses($html): string
{
  return wp_kses((string) $html, dashboard_allowed_inline_html());
}

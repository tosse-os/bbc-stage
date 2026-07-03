<?php

/**
 * Prüft, ob ein Dashboard-Navigationspunkt aktiv ist
 */
function dashboard_active(string $path, bool $exact = false): bool
{
  $current = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
  $path = trim($path, '/');

  if ($exact) {
    return $current === $path;
  }

  return $current === $path || str_starts_with($current, $path . '/');
}

function dashboard_allowed_langs(): array
{
  return ['de', 'en'];
}

function dashboard_default_lang(): string
{
  return 'de';
}

function dashboard_lang(): string
{
  $allowed = dashboard_allowed_langs();
  $raw = $_REQUEST['lang'] ?? '';
  $lang = sanitize_key(is_string($raw) ? wp_unslash($raw) : '');

  if (in_array($lang, $allowed, true)) {
    return $lang;
  }

  if (function_exists('pll_current_language')) {
    $pllLang = sanitize_key((string) pll_current_language('slug'));

    if (in_array($pllLang, $allowed, true)) {
      return $pllLang;
    }
  }

  return dashboard_default_lang();
}

function dashboard_i18n_file(string $lang): string
{
  $lang = in_array($lang, dashboard_allowed_langs(), true) ? $lang : dashboard_default_lang();

  return get_theme_file_path('resources/i18n/dashboard-' . $lang . '.json');
}

function dashboard_i18n_catalog(string $lang = ''): array
{
  static $cache = [];

  $lang = $lang !== '' ? $lang : dashboard_lang();
  $lang = in_array($lang, dashboard_allowed_langs(), true) ? $lang : dashboard_default_lang();

  if (isset($cache[$lang])) {
    return $cache[$lang];
  }

  $file = dashboard_i18n_file($lang);

  if (!is_readable($file) && $lang !== dashboard_default_lang()) {
    return dashboard_i18n_catalog(dashboard_default_lang());
  }

  if (!is_readable($file)) {
    $cache[$lang] = [];

    return $cache[$lang];
  }

  $json = json_decode((string) file_get_contents($file), true);
  $cache[$lang] = is_array($json) ? $json : [];

  return $cache[$lang];
}

function dashboard_array_get(array $data, string $key)
{
  $current = $data;

  foreach (explode('.', $key) as $segment) {
    if (!is_array($current) || !array_key_exists($segment, $current)) {
      return null;
    }

    $current = $current[$segment];
  }

  return $current;
}

function dashboard_t(string $key, string $fallback = '', array $replace = []): string
{
  $value = dashboard_array_get(dashboard_i18n_catalog(), $key);

  if (!is_string($value) || $value === '') {
    $value = dashboard_array_get(dashboard_i18n_catalog(dashboard_default_lang()), $key);
  }

  if (!is_string($value) || $value === '') {
    $value = $fallback !== '' ? $fallback : $key;
  }

  foreach ($replace as $name => $replacement) {
    $value = str_replace(':' . $name, (string) $replacement, $value);
  }

  return $value;
}

function dashboard_lang_query_arg(array $args = []): array
{
  $args['lang'] = $args['lang'] ?? dashboard_lang();

  return $args;
}

function dashboard_url(string $path = 'dashboard', array $args = []): string
{
  $path = trim($path);

  if ($path === '') {
    $path = 'dashboard';
  }

  $path = '/' . trim($path, '/');
  $url = home_url($path);

  return add_query_arg(dashboard_lang_query_arg($args), $url);
}

function dashboard_current_url_for_lang(string $lang): string
{
  $lang = in_array($lang, dashboard_allowed_langs(), true) ? $lang : dashboard_default_lang();
  $request = $_SERVER['REQUEST_URI'] ?? '/dashboard';
  $path = parse_url($request, PHP_URL_PATH) ?: '/dashboard';
  $query = parse_url($request, PHP_URL_QUERY) ?: '';
  $args = [];

  if ($query !== '') {
    parse_str($query, $args);
  }

  $args = array_filter($args, static fn($value) => is_scalar($value));
  $args['lang'] = $lang;

  return add_query_arg($args, home_url($path));
}

function dashboard_languages(): array
{
  return array_map(static function (string $lang): array {
    return [
      'slug' => $lang,
      'label' => strtoupper($lang),
      'current' => dashboard_lang() === $lang,
      'url' => dashboard_current_url_for_lang($lang),
    ];
  }, dashboard_allowed_langs());
}

function dashboard_login_url(array $args = []): string
{
  return dashboard_url('dashboard-login', $args);
}

function dashboard_register_url(array $args = []): string
{
  return dashboard_url('dashboard-register', $args);
}

function dashboard_password_url(array $args = []): string
{
  return dashboard_url('dashboard-password', $args);
}

function dashboard_settings_url(array $args = []): string
{
  return dashboard_url('dashboard-settings', $args);
}

function dashboard_settings_billing_url(array $args = []): string
{
  return dashboard_settings_url(array_merge(['tab' => 'billing'], $args));
}

function dashboard_logout_url(): string
{
  return add_query_arg([
    'dashboard_logout' => '1',
    '_wpnonce' => wp_create_nonce('dashboard_logout'),
    'lang' => dashboard_lang(),
  ], home_url('/'));
}

function dashboard_redirect(string $path, array $args = []): void
{
  wp_safe_redirect(dashboard_url($path, $args));
  exit;
}

function dashboard_error_text(string $code, string $fallbackKey = 'errors.generic'): string
{
  $code = sanitize_key($code);

  if ($code !== '') {
    $message = dashboard_t('errors.' . $code);

    if ($message !== 'errors.' . $code) {
      return $message;
    }
  }

  return dashboard_t($fallbackKey);
}

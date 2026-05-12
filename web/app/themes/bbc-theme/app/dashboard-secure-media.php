<?php

function dashboard_acf_attachment_id($value): int
{
  if (is_numeric($value)) {
    return (int) $value;
  }

  if (!is_array($value)) {
    return 0;
  }

  if (!empty($value['ID'])) {
    return (int) $value['ID'];
  }

  if (!empty($value['id'])) {
    return (int) $value['id'];
  }

  return 0;
}

function dashboard_secure_media_url($attachment, string $size = ''): string
{
  $attachment_id = dashboard_acf_attachment_id($attachment);

  if ($attachment_id <= 0) {
    return '';
  }

  return add_query_arg([
    'dashboard_secure_media' => '1',
    'media_id' => $attachment_id,
    'size' => $size,
    '_wpnonce' => wp_create_nonce('dashboard_secure_media_' . $attachment_id),
  ], home_url('/'));
}

function dashboard_secure_media_forbidden(): void
{
  status_header(403);
  nocache_headers();
  header('Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0');
  header('X-Content-Type-Options: nosniff');
  header('X-Robots-Tag: noindex, nofollow, noarchive');
  echo 'Forbidden';
  exit;
}

function dashboard_secure_media_file_for_size(int $attachment_id, string $size): string
{
  $file = get_attached_file($attachment_id);

  if (!is_string($file) || $file === '') {
    return '';
  }

  if ($size === '') {
    return $file;
  }

  $mime = (string) get_post_mime_type($attachment_id);

  if (!str_starts_with($mime, 'image/')) {
    return $file;
  }

  $meta = wp_get_attachment_metadata($attachment_id);

  if (!is_array($meta) || empty($meta['sizes'][$size]['file'])) {
    return $file;
  }

  $candidate = trailingslashit(dirname($file)) . $meta['sizes'][$size]['file'];

  return is_readable($candidate) ? $candidate : $file;
}

function dashboard_stream_secure_media_file(string $file, string $mime): void
{
  if (!is_readable($file)) {
    status_header(404);
    nocache_headers();
    echo 'Not found';
    exit;
  }

  $size = filesize($file);

  if (!$size) {
    status_header(404);
    nocache_headers();
    echo 'Not found';
    exit;
  }

  $start = 0;
  $end = $size - 1;
  $status = 200;

  if (!empty($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
    if ($matches[1] !== '') {
      $start = (int) $matches[1];
    }

    if ($matches[2] !== '') {
      $end = (int) $matches[2];
    }

    if ($start > $end || $start >= $size) {
      status_header(416);
      header('Content-Range: bytes */' . $size);
      exit;
    }

    $status = 206;
  }

  $length = $end - $start + 1;

  status_header($status);
  header('Content-Type: ' . $mime);
  header('Content-Length: ' . $length);
  header('Accept-Ranges: bytes');
  header('X-Content-Type-Options: nosniff');
  header('Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');
  header('Expires: 0');
  header('X-Robots-Tag: noindex, nofollow, noarchive');

  if ($status === 206) {
    header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
  }

  $handle = fopen($file, 'rb');

  if (!$handle) {
    status_header(500);
    exit;
  }

  fseek($handle, $start);

  $remaining = $length;

  while ($remaining > 0 && !feof($handle)) {
    $chunk_size = min(8192, $remaining);
    echo fread($handle, $chunk_size);
    $remaining -= $chunk_size;
    flush();
  }

  fclose($handle);
  exit;
}

add_action('template_redirect', function () {
  if (empty($_GET['dashboard_secure_media'])) {
    return;
  }

  $attachment_id = absint($_GET['media_id'] ?? 0);

  if ($attachment_id <= 0) {
    dashboard_secure_media_forbidden();
  }

  if (!is_user_logged_in()) {
    dashboard_secure_media_forbidden();
  }

  if (
    empty($_GET['_wpnonce']) ||
    !wp_verify_nonce($_GET['_wpnonce'], 'dashboard_secure_media_' . $attachment_id)
  ) {
    dashboard_secure_media_forbidden();
  }

  if (function_exists('dashboard_maybe_sync_user_subscription_before_access')) {
    dashboard_maybe_sync_user_subscription_before_access(get_current_user_id());
  }

  if (!function_exists('dashboard_user_has_premium_access') || !dashboard_user_has_premium_access(get_current_user_id())) {
    dashboard_secure_media_forbidden();
  }

  if (get_post_type($attachment_id) !== 'attachment') {
    dashboard_secure_media_forbidden();
  }

  $mime = (string) get_post_mime_type($attachment_id);

  $allowed = [
    'image/jpeg',
    'image/png',
    'image/webp',
    'image/gif',
    'audio/mpeg',
    'audio/mp4',
    'audio/x-m4a',
    'audio/wav',
    'audio/ogg',
  ];

  if (!in_array($mime, $allowed, true)) {
    dashboard_secure_media_forbidden();
  }

  $size = sanitize_key($_GET['size'] ?? '');
  $file = dashboard_secure_media_file_for_size($attachment_id, $size);

  dashboard_stream_secure_media_file($file, $mime);
});

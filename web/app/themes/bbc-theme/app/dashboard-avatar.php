<?php

add_action('wp_ajax_dashboard_upload_avatar', function () {

  if (!is_user_logged_in()) {
    wp_send_json_error();
  }

  if (
    !isset($_POST['_wpnonce']) ||
    !wp_verify_nonce($_POST['_wpnonce'], 'dashboard_avatar_upload')
  ) {
    wp_send_json_error();
  }

  if (empty($_FILES['avatar'])) {
    wp_send_json_error();
  }

  $rateLimitIdentity = (string) get_current_user_id();

  if (function_exists('dashboard_rate_limit_hit') && dashboard_rate_limit_hit('dashboard_avatar_upload', $rateLimitIdentity, 10, HOUR_IN_SECONDS)) {
    wp_send_json_error(['message' => 'rate_limited']);
  }

  $file = $_FILES['avatar'];

  if (!empty($file['error'])) {
    wp_send_json_error(['message' => 'upload_error']);
  }

  if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
    wp_send_json_error(['message' => 'invalid_upload']);
  }

  if ($file['size'] > 2 * 1024 * 1024) {
    wp_send_json_error(['message' => 'file_too_large']);
  }

  $allowed = ['image/jpeg', 'image/png', 'image/webp'];
  $check = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);

  if (!$check['ext'] || !in_array($check['type'], $allowed, true)) {
    wp_send_json_error(['message' => 'invalid_type']);
  }

  $imageMime = function_exists('wp_get_image_mime') ? wp_get_image_mime($file['tmp_name']) : '';

  if ($imageMime === '' || !in_array($imageMime, $allowed, true)) {
    wp_send_json_error(['message' => 'invalid_image']);
  }

  $dimensions = @getimagesize($file['tmp_name']);

  if (!is_array($dimensions) || empty($dimensions[0]) || empty($dimensions[1]) || $dimensions[0] > 4000 || $dimensions[1] > 4000) {
    wp_send_json_error(['message' => 'invalid_image']);
  }

  require_once ABSPATH . 'wp-admin/includes/file.php';
  require_once ABSPATH . 'wp-admin/includes/image.php';

  $upload = wp_handle_upload($file, [
    'test_form' => false,
    'mimes' => [
      'jpg|jpeg' => 'image/jpeg',
      'png' => 'image/png',
      'webp' => 'image/webp',
    ],
  ]);

  if (!empty($upload['error'])) {
    wp_send_json_error();
  }

  $attachment_id = wp_insert_attachment([
    'post_mime_type' => $upload['type'],
    'post_title' => basename($upload['file']),
    'post_content' => '',
    'post_status' => 'inherit',
  ], $upload['file']);

  wp_update_attachment_metadata(
    $attachment_id,
    wp_generate_attachment_metadata($attachment_id, $upload['file'])
  );

  $user_id = get_current_user_id();

  $old = get_user_meta($user_id, 'dashboard_avatar_id', true);
  if ($old) {
    wp_delete_attachment((int) $old, true);
  }

  update_user_meta($user_id, 'dashboard_avatar_id', $attachment_id);

  wp_send_json_success([
    'url' => wp_get_attachment_image_url($attachment_id, 'thumbnail')
  ]);
});

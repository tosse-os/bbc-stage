<?php

add_action('init', function () {

  register_post_type('media_entry', [
    'labels' => [
      'name' => __('Media Entries'),
      'singular_name' => __('Media Entry'),
    ],
    'public' => false,
    'show_ui' => true,
    'show_in_menu' => true,
    'supports' => ['title'],
    'has_archive' => false,
    'rewrite' => false,
  ]);
});


/*
|--------------------------------------------------------------------------
| Auto-Title from Media File (Stable)
|--------------------------------------------------------------------------
| Setzt beim Speichern eines Media Entry automatisch den Dateinamen
| als Titel, wenn kein Titel gesetzt ist.
|--------------------------------------------------------------------------
*/

add_action('save_post_media_entry', function ($post_id, $post, $update) {

  if (wp_is_post_revision($post_id)) {
    return;
  }

  $currentTitle = get_post_field('post_title', $post_id);

  if (!empty(trim($currentTitle))) {
    return;
  }

  $mediaType = get_field('media_type', $post_id);
  $filename = null;

  if ($mediaType === 'audio') {
    $audio = get_field('audio_file', $post_id);
    if (!empty($audio['filename'])) {
      $filename = $audio['filename'];
    }
  }

  if ($mediaType === 'video') {
    $videoUrl = get_field('video_url', $post_id);
    if (!empty($videoUrl)) {
      $filename = basename(parse_url($videoUrl, PHP_URL_PATH));
    }
  }

  if (!$filename) {
    return;
  }

  $title = pathinfo($filename, PATHINFO_FILENAME);

  $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $title = wp_strip_all_tags($title);
  $title = str_replace(['-', '_'], ' ', $title);
  $title = preg_replace('/\s+/', ' ', $title);
  $title = trim($title);

  remove_action('save_post_media_entry', __FUNCTION__, 10);

  wp_update_post([
    'ID' => $post_id,
    'post_title' => $title
  ]);

  add_action('save_post_media_entry', __FUNCTION__, 10);
}, 10, 3);



/*
|--------------------------------------------------------------------------
| Default Publish Date
|--------------------------------------------------------------------------
| Setzt beim Anlegen eines neuen Media Entry automatisch das aktuelle
| Datum als Standardwert für publish_date.
|--------------------------------------------------------------------------
*/

add_filter('acf/load_value/name=publish_date', function ($value, $post_id) {

  if (!$value && get_post_type($post_id) === 'media_entry') {
    return current_time('Y-m-d');
  }

  return $value;
}, 10, 2);

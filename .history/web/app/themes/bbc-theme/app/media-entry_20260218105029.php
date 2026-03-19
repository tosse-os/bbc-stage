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
| Auto-Title from Media File
|--------------------------------------------------------------------------
| Wenn ein Media Entry gespeichert wird und kein Titel gesetzt ist,
| wird automatisch der Dateiname (ohne Endung) als Titel übernommen.
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Auto-Title from Media File
|--------------------------------------------------------------------------
| Übernimmt beim Speichern eines Media Entry automatisch den Dateinamen
| (ohne Endung) als Titel, wenn kein Titel gesetzt wurde.
| HTML-Entities werden bereinigt und der Titel wird direkt im Backend
| sichtbar gesetzt.
|--------------------------------------------------------------------------
*/

add_action('acf/save_post', function ($post_id) {

  if (get_post_type($post_id) !== 'media_entry') {
    return;
  }

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

  remove_action('acf/save_post', __FUNCTION__, 20);

  wp_update_post([
    'ID' => $post_id,
    'post_title' => $title
  ]);

  add_action('acf/save_post', __FUNCTION__, 20);
}, 20);

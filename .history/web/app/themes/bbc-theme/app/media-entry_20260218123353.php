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
    'supports' => ['title', 'editor'],
    'has_archive' => false,
    'rewrite' => false,
  ]);
});


/*
|--------------------------------------------------------------------------
| Auto-Title from Media File (Stable Fallback)
|--------------------------------------------------------------------------
| Setzt beim Speichern eines Media Entry automatisch den Dateinamen
| als Titel, wenn kein Titel gesetzt ist.
|--------------------------------------------------------------------------
*/

add_action('acf/save_post', function ($post_id) {
  if (get_post_type($post_id) !== 'media_entry' || wp_is_post_revision($post_id)) {
    return;
  }

  $currentTitle = get_post_field('post_title', $post_id);
  // Falls das JS im Browser mal blockiert wird, greift dieser Check:
  if (!empty(trim($currentTitle)) && $currentTitle !== 'Auto Draft' && $currentTitle !== 'Titel hinzufügen') {
    return;
  }

  $audio = get_field('audio_file', $post_id);
  if (empty($audio['filename'])) {
    return;
  }

  // Deine bewährte Bereinigungs-Logik
  $filename = $audio['filename'];
  $title = pathinfo($filename, PATHINFO_FILENAME);
  $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $title = str_replace(['-', '_'], ' ', $title);
  $title = ucwords(trim(preg_replace('/\s+/', ' ', $title)));

  remove_action('acf/save_post', __FUNCTION__, 20);
  wp_update_post([
    'ID'         => $post_id,
    'post_title' => $title,
    'post_name'  => sanitize_title($title) // Erstellt auch die passende URL
  ]);
  add_action('acf/save_post', __FUNCTION__, 20);
}, 20);


/*
|--------------------------------------------------------------------------
| Default Publish Date (Smart)
|--------------------------------------------------------------------------
| Setzt beim Anlegen eines neuen Media Entry automatisch das aktuelle
| Datum, lässt bestehende Werte beim Bearbeiten aber unverändert.
|--------------------------------------------------------------------------
*/

add_filter('acf/load_value/name=publish_date', function ($value, $post_id) {

  // 1. Wenn bereits ein Wert in der Datenbank steht, diesen behalten
  if (!empty($value)) {
    return $value;
  }

  // 2. Prüfen, ob wir uns im Backend befinden und es ein media_entry ist
  if (is_admin() && get_post_type($post_id) === 'media_entry') {

    // 3. Nur wenn der Post brandneu ist (Status 'auto-draft' oder gar kein Status)
    $post_status = get_post_status($post_id);

    if (!$post_status || $post_status === 'auto-draft') {
      return current_time('Y-m-d');
    }
  }

  return $value;
}, 10, 2);

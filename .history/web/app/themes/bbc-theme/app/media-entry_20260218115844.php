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
| Auto-Title from Media File (Stable Fallback)
|--------------------------------------------------------------------------
| Setzt beim Speichern eines Media Entry automatisch den Dateinamen
| als Titel, wenn kein Titel gesetzt ist.
|--------------------------------------------------------------------------
*/

add_action('save_post_media_entry', function ($post_id, $post, $update) {
  if (wp_is_post_revision($post_id) || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // Holen des aktuellen Titels direkt aus der Datenbank für maximale Genauigkeit
  $currentTitle = trim(get_post_field('post_title', $post_id));

  // Falls WP "Auto Draft" oder den Standard-Platzhalter gesetzt hat
  $placeholders = ['Auto Draft', 'Titel hinzufügen', 'Draft'];

  if (!empty($currentTitle) && !in_array($currentTitle, $placeholders)) {
    return;
  }

  $audio = get_field('audio_file', $post_id);
  if (!$audio || empty($audio['filename'])) {
    return;
  }

  $title = pathinfo($audio['filename'], PATHINFO_FILENAME);
  $title = ucwords(str_replace(['-', '_'], ' ', $title));

  // Action kurz entfernen um Endlosschleife zu verhindern
  remove_action('save_post_media_entry', __FUNCTION__, 20);

  wp_update_post([
    'ID'         => $post_id,
    'post_title' => $title,
    'post_name'  => sanitize_title($title)
  ]);

  add_action('save_post_media_entry', __FUNCTION__, 20);
}, 20, 3); // Priorität auf 20 erhöht


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

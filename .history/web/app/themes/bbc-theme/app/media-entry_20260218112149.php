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

/*
|--------------------------------------------------------------------------
| Auto-Title from Media File (Immediate)
|--------------------------------------------------------------------------
| Setzt beim Speichern eines Media Entry den Dateinamen als Titel,
| bevor WordPress speichert. HTML-Entities werden vollständig bereinigt.
|--------------------------------------------------------------------------
*/

add_filter('wp_insert_post_data', function ($data, $postarr) {

  if ($data['post_type'] !== 'media_entry') {
    return $data;
  }

  if (!empty(trim($data['post_title']))) {
    return $data;
  }

  if (empty($_POST['acf'])) {
    return $data;
  }

  $acf = $_POST['acf'];
  $filename = null;

  foreach ($acf as $fieldKey => $value) {

    if (is_array($value) && isset($value['filename'])) {
      $filename = $value['filename'];
      break;
    }

    if (filter_var($value, FILTER_VALIDATE_URL)) {
      $filename = basename(parse_url($value, PHP_URL_PATH));
      break;
    }
  }

  if (!$filename) {
    return $data;
  }

  $title = pathinfo($filename, PATHINFO_FILENAME);

  $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  $title = wp_specialchars_decode($title);
  $title = wp_strip_all_tags($title);
  $title = str_replace(['-', '_'], ' ', $title);
  $title = preg_replace('/\s+/', ' ', $title);
  $title = trim($title);

  $data['post_title'] = $title;

  return $data;
}, 10, 2);


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

<?php

/**
 * Validiert das Chart-Bild für Analysen.
 * Erzwingt Mindestgröße und Querformat (Aspect Ratio).
 */

add_filter('acf/validate_value/name=chart_image', function ($valid, $value, $field, $input) {

  if ($valid !== true) {
    return $valid;
  }

  if (empty($value)) {
    return $valid;
  }

  if (is_array($value) && isset($value['ID'])) {
    $attachment_id = (int) $value['ID'];
  } elseif (is_numeric($value)) {
    $attachment_id = (int) $value;
  } else {
    return 'Ungültiges Bildformat.';
  }

  $meta = wp_get_attachment_metadata($attachment_id);

  if (empty($meta['width']) || empty($meta['height'])) {
    return 'Das Bild konnte nicht geprüft werden.';
  }

  $width  = (int) $meta['width'];
  $height = (int) $meta['height'];

  if ($width < 2400 || $height < 1000) {
    return 'Das Chart-Bild ist zu klein. Mindestgröße: 2400 × 1000 Pixel.';
  }

  if (($width / $height) < 2.2) {
    return 'Das Chart-Bild muss im Querformat vorliegen.';
  }

  return true;

}, 10, 4);

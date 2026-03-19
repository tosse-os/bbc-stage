<?php

/**
 * Validiert das Chart-Bild für Analysen.
 * Erzwingt Mindestgröße und Querformat (Aspect Ratio).
 */

add_filter('acf/validate_value/name=chart_image', function ($valid, $value, $field, $input) {

  if ($valid !== true) {
    return $valid;
  }

  if (empty($value) || !is_array($value) || empty($value['ID'])) {
    return $valid;
  }

  $attachment_id = (int) $value['ID'];

  $meta = wp_get_attachment_metadata($attachment_id);
  if (empty($meta['width']) || empty($meta['height'])) {
    return 'Das Chart-Bild konnte nicht validiert werden.';
  }

  $width  = (int) $meta['width'];
  $height = (int) $meta['height'];

  if ($width < 2400 || $height < 1000) {
    return 'Das Chart-Bild ist zu klein. Mindestgröße: 2400 × 1000 Pixel.';
  }

  $ratio = $width / $height;

  if ($ratio < 2.2) {
    return 'Das Chart-Bild muss im Querformat vorliegen (zu hoch oder zu schmal).';
  }

  return true;
}, 10, 4);

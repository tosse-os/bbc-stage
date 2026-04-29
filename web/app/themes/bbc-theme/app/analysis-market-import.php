<?php

add_action('admin_init', function () {

  if (empty($_FILES['market_json'])) {
    return;
  }

  if (
    !current_user_can('manage_options') ||
    !isset($_POST['analysis_market_import_nonce']) ||
    !wp_verify_nonce($_POST['analysis_market_import_nonce'], 'analysis_market_import')
  ) {
    wp_die('Nicht erlaubt.');
  }

  $json = file_get_contents($_FILES['market_json']['tmp_name']);
  $data = json_decode($json, true);

  if (!is_array($data)) {
    wp_die('Ungültiges JSON.');
  }

  $map = [];
  $log = [];

  foreach ($data as $item) {
    import_market_phase1($item, $map, $log);
  }

  foreach ($data as $item) {
    import_market_phase2($item, $map, $log);
  }

  set_transient('analysis_market_import_log', $log, 120);

  wp_safe_redirect(admin_url('admin.php?page=analysis-market-import&import=done'));
  exit;
});

function import_market_phase1(array $item, array &$map, array &$log)
{
  if (empty($item['id']) || empty($item['name']) || empty($item['slug'])) {
    $log[] = "❌ Ungültiger Eintrag (fehlende id/name/slug)";
    return;
  }

  $existing = get_terms([
    'taxonomy'   => 'analysis_market',
    'hide_empty' => false,
    'meta_query' => [[
      'key'     => 'market_uid',
      'value'   => (string) $item['id'],
      'compare' => '=',
    ]],
  ]);

  if (is_wp_error($existing)) {
    $log[] = "❌ Fehler bei Suche nach market_uid {$item['id']}";
    return;
  }

  if (count($existing) > 1) {
    $log[] = "❌ Mehrdeutige market_uid {$item['id']} – Import übersprungen";
    return;
  }

  if (count($existing) === 1) {
    $term_id = $existing[0]->term_id;

    $updated = wp_update_term($term_id, 'analysis_market', [
      'name' => sanitize_text_field($item['name']),
      'slug' => sanitize_title($item['slug']),
    ]);

    if (is_wp_error($updated)) {
      $log[] = "❌ Fehler bei {$item['name']}: " . $updated->get_error_message();
      return;
    }

    $log[] = "🔄 aktualisiert: {$item['name']} ({$item['id']})";
  } else {
    $created = wp_insert_term(sanitize_text_field($item['name']), 'analysis_market', [
      'slug' => sanitize_title($item['slug']),
    ]);

    if (is_wp_error($created)) {
      $log[] = "❌ Fehler bei {$item['name']}: " . $created->get_error_message();
      return;
    }

    $term_id = $created['term_id'];
    add_term_meta($term_id, 'market_uid', (string) $item['id'], true);

    $log[] = "✔️ angelegt: {$item['name']} ({$item['id']})";
  }

  if (!empty($item['meta']) && is_array($item['meta'])) {
    foreach ($item['meta'] as $key => $value) {
      update_term_meta($term_id, sanitize_key($key), sanitize_text_field((string) $value));
    }
  }

  $map[(string) $item['id']] = $term_id;

  if (!empty($item['children']) && is_array($item['children'])) {
    foreach ($item['children'] as $child) {
      import_market_phase1($child, $map, $log);
    }
  }
}

function import_market_phase2(array $item, array $map, array &$log)
{
  if (empty($item['id'])) {
    return;
  }

  $id = (string) $item['id'];

  if (!isset($map[$id])) {
    $log[] = "❌ Parent-Zuordnung fehlgeschlagen: {$id} nicht im Map";
    return;
  }

  if (!empty($item['parent_id'])) {
    $parent_id = (string) $item['parent_id'];

    if (!isset($map[$parent_id])) {
      $log[] = "❌ Ungültiger Parent {$parent_id} für {$id}";
    } else {
      $updated = wp_update_term($map[$id], 'analysis_market', [
        'parent' => $map[$parent_id],
      ]);

      if (is_wp_error($updated)) {
        $log[] = "❌ Parent konnte nicht gesetzt werden: {$id} → {$parent_id}";
      } else {
        $log[] = "↳ Parent gesetzt: {$id} → {$parent_id}";
      }
    }
  }

  if (!empty($item['children']) && is_array($item['children'])) {
    foreach ($item['children'] as $child) {
      import_market_phase2($child, $map, $log);
    }
  }
}

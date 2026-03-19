<?php

/**
 * Markt-Import: zweiphasig
 * Phase 1: Terms anlegen / aktualisieren (ohne Parent)
 * Phase 2: Parent-Child-Zuordnung über stabile externe IDs
 */

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

  if (!$data || !is_array($data)) {
    wp_die('Ungültiges JSON.');
  }

  $map  = [];
  $log  = [];

  foreach ($data as $item) {
    analysis_market_import_phase1($item, $map, $log);
  }

  foreach ($data as $item) {
    analysis_market_import_phase2($item, $map, $log);
  }

  set_transient('analysis_market_import_log', $log, 60);

  wp_redirect(admin_url('edit.php?post_type=analysis&page=analysis-market-import&import=done'));
  exit;
});

/**
 * Phase 1: Term anlegen oder aktualisieren
 */
function analysis_market_import_phase1(array $item, array &$map, array &$log)
{
  $existing = get_terms([
    'taxonomy'   => 'analysis_market',
    'hide_empty' => false,
    'meta_query' => [
      [
        'key'   => 'market_uid',
        'value' => $item['id'],
      ]
    ]
  ]);

  if (!empty($existing)) {
    $term_id = $existing[0]->term_id;

    wp_update_term($term_id, 'analysis_market', [
      'name' => $item['name'],
    ]);

    $log[] = "🔄 aktualisiert: {$item['name']} ({$item['id']})";
  } else {
    $created = wp_insert_term($item['name'], 'analysis_market');

    if (is_wp_error($created)) {
      $log[] = "❌ Fehler bei {$item['name']}: " . $created->get_error_message();
      return;
    }

    $term_id = $created['term_id'];
    add_term_meta($term_id, 'market_uid', $item['id'], true);

    $log[] = "✔️ angelegt: {$item['name']} ({$item['id']})";
  }

  if (!empty($item['meta'])) {
    foreach ($item['meta'] as $key => $value) {
      update_term_meta($term_id, $key, $value);
    }
  }

  $map[$item['id']] = $term_id;

  if (!empty($item['children'])) {
    foreach ($item['children'] as $child) {
      analysis_market_import_phase1($child, $map, $log);
    }
  }
}

/**
 * Phase 2: Parent-Child-Zuordnung
 */
function analysis_market_import_phase2(array $item, array $map, array &$log, int $parent = 0)
{
  if (!isset($map[$item['id']])) {
    return;
  }

  $term_id = $map[$item['id']];

  if ($parent > 0) {
    wp_update_term($term_id, 'analysis_market', [
      'parent' => $parent,
    ]);
  }

  if (!empty($item['children'])) {
    foreach ($item['children'] as $child) {
      analysis_market_import_phase2($child, $map, $log, $term_id);
    }
  }
}

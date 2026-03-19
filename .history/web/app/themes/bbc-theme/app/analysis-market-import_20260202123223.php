<?php

/**
 * Verarbeitet den Upload und startet den Import
 */
add_action('admin_init', function () {

  // Kein Upload → raus
  if (empty($_FILES['market_json'])) {
    return;
  }

  // Sicherheitscheck
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

  foreach ($data as $market) {
    analysis_market_import_term($market);
  }

  wp_redirect(admin_url('edit.php?post_type=analysis&page=analysis-market-import&import=success'));
  exit;
});

/**
 * Importiert einen Markt (rekursiv)
 *
 * Erwartetes Format:
 * [
 *   id, name, meta, children[]
 * ]
 */
function analysis_market_import_term(array $item, int $parent = 0)
{
  // Existiert Term bereits anhand stabiler ID?
  $existing = get_terms([
    'taxonomy' => 'analysis_market',
    'hide_empty' => false,
    'meta_query' => [
      [
        'key' => 'market_uid',
        'value' => $item['id'],
      ]
    ]
  ]);

  if (!empty($existing)) {
    $term_id = $existing[0]->term_id;

    wp_update_term($term_id, 'analysis_market', [
      'name' => $item['name'],
      'parent' => $parent,
    ]);
  } else {
    $created = wp_insert_term($item['name'], 'analysis_market', [
      'parent' => $parent,
    ]);

    if (is_wp_error($created)) {
      return;
    }

    $term_id = $created['term_id'];
    add_term_meta($term_id, 'market_uid', $item['id'], true);
  }

  // Meta-Daten (WKN, ISIN etc.)
  if (!empty($item['meta'])) {
    foreach ($item['meta'] as $key => $value) {
      update_term_meta($term_id, $key, $value);
    }
  }

  // Kinder rekursiv importieren
  if (!empty($item['children'])) {
    foreach ($item['children'] as $child) {
      analysis_market_import_term($child, $term_id);
    }
  }
}

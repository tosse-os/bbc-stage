<?php

/**
 * Fügt im Analysis-Menü einen Importpunkt hinzu
 * Zugriff ausschließlich für Administratoren
 */
add_action('admin_menu', function () {

  add_submenu_page(
    'edit.php?post_type=analysis',
    'Asset-Import',
    'Asset-Import',
    'manage_options',
    'analysis-market-import',
    'analysis_market_import_page'
  );
});

/**
 * Rendert die Markt-Import-Seite im Admin
 * Enthält Upload-Formular und Import-Log-Ausgabe
 */
function analysis_market_import_page()
{
?>
  <div class="wrap">

    <h1>Märkte importieren</h1>

    <p>
      Importiert hierarchische Marktstrukturen aus einer JSON-Datei.
      Bestehende Märkte werden anhand ihrer stabilen externen IDs aktualisiert.
    </p>

    <form method="post" enctype="multipart/form-data">
      <?php wp_nonce_field('analysis_market_import', 'analysis_market_import_nonce'); ?>

      <input type="file" name="market_json" accept="application/json" required>

      <p>
        <button class="button button-primary">Import starten</button>
      </p>
    </form>

    <?php
    /**
     * Gibt das Import-Log aus, falls ein Import durchgeführt wurde
     */
    if (isset($_GET['import']) && $_GET['import'] === 'done') {

      $log = get_transient('analysis_market_import_log');

      if ($log && is_array($log)) {
    ?>
        <h2>Import-Protokoll</h2>
        <pre style="background:#111;color:#0f0;padding:15px;max-height:500px;overflow:auto;">
<?php echo esc_html(implode("\n", $log)); ?>
        </pre>
      <?php
      } else {
      ?>
        <p><em>Kein Import-Log verfügbar.</em></p>
    <?php
      }
    }
    ?>

  </div>
  <?php
}





// app/analysis-market-admin.php

add_action('admin_menu', function () {
  add_submenu_page(
    'edit.php?post_type=analysis',
    'Import-Kontrolle',
    'Import-Kontrolle',
    'manage_options',
    'analysis-import-control',
    'analysis_import_control_view'
  );
});

function analysis_import_control_view() {
  if (!current_user_can('manage_options')) return;

  $search = isset($_GET['s']) ? trim(sanitize_text_field($_GET['s'])) : '';

  $all_terms = get_terms([
    'taxonomy'   => 'analysis_market',
    'hide_empty' => false,
    'orderby'    => 'parent',
    'order'      => 'ASC',
  ]);

  $search_lc = mb_strtolower($search);

  $terms = [];
  foreach ($all_terms as $t) {
    if ($search_lc === '') {
      $terms[] = $t;
      continue;
    }

    $name_lc = mb_strtolower($t->name);

    if (strpos($name_lc, $search_lc) !== false) {
      $terms[] = $t;
      continue;
    }

    if ($t->parent) {
      $p = get_term($t->parent, 'analysis_market');
      if ($p && !is_wp_error($p)) {
        if (strpos(mb_strtolower($p->name), $search_lc) !== false) {
          $terms[] = $t;
        }
      }
    }
  }

  $terms_by_id = [];
  foreach ($terms as $t) {
    $terms_by_id[$t->term_id] = $t;
  }

  if ($search_lc !== '') {
    foreach ($terms as $t) {
      $pid = $t->parent;
      while ($pid && !isset($terms_by_id[$pid])) {
        $p = get_term($pid, 'analysis_market');
        if (!$p || is_wp_error($p)) break;
        $terms_by_id[$p->term_id] = $p;
        $pid = $p->parent;
      }
    }
  }

  $tree = [];
  foreach ($terms_by_id as $t) {
    $tree[$t->parent][] = $t;
  }

  function render_rows($parent_id, $tree, $level = 0) {
    if (!isset($tree[$parent_id])) return;

    foreach ($tree[$parent_id] as $term) {
      $indent = str_repeat('— ', $level);

      $lang = '';
      if (function_exists('pll_get_term_language')) {
        $lang = pll_get_term_language($term->term_id);
      } elseif (function_exists('wpml_get_language_information')) {
        $info = wpml_get_language_information(null, $term->term_id);
        $lang = $info['language_code'] ?? '';
      }

      $wkn  = get_term_meta($term->term_id, 'wkn', true);
      $isin = get_term_meta($term->term_id, 'isin', true);
      ?>
      <tr>
        <td><?php echo esc_html($term->term_id); ?></td>
        <td><?php echo esc_html($indent . $term->name); ?></td>
        <td><?php echo esc_html($lang ?: '—'); ?></td>
        <td><?php echo esc_html($wkn ?: '—'); ?></td>
        <td><?php echo esc_html($isin ?: '—'); ?></td>
      </tr>
      <?php
      render_rows($term->term_id, $tree, $level + 1);
    }
  }
  ?>
  <div class="wrap">
    <h1>Import-Kontrolle – Analysis Markets</h1>

    <form method="get" style="margin-bottom:12px;">
      <input type="hidden" name="post_type" value="analysis">
      <input type="hidden" name="page" value="analysis-import-control">
      <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Name oder Teilstring">
      <button class="button">Suchen</button>
    </form>

    <table class="widefat fixed striped">
      <thead>
        <tr>
          <th>Term-ID</th>
          <th>Name (Hierarchie)</th>
          <th>Sprache</th>
          <th>WKN</th>
          <th>ISIN</th>
        </tr>
      </thead>
      <tbody>
        <?php render_rows(0, $tree, 0); ?>
      </tbody>
    </table>
  </div>
  <?php
}

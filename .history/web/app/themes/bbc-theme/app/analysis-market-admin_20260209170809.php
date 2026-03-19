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





/* check area */

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

function analysis_import_control_view()
{
  if (!current_user_can('manage_options')) return;

  $terms = get_terms([
    'taxonomy'   => 'analysis_market',
    'hide_empty' => false,
    'orderby'    => 'parent',
    'order'      => 'ASC',
  ]);
?>
  <div class="wrap">
    <h1>Import-Kontrolle – Analysis Markets</h1>

    <table class="widefat fixed striped">
      <thead>
        <tr>
          <th>Term-ID</th>
          <th>Name</th>
          <th>Parent</th>
          <th>Sprache</th>
          <th>WKN</th>
          <th>ISIN</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($terms as $term): ?>
          <?php
          $parent = $term->parent ? get_term($term->parent, 'analysis_market') : null;

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
            <td><?php echo esc_html($term->name); ?></td>
            <td><?php echo esc_html($parent ? $parent->name : '—'); ?></td>
            <td><?php echo esc_html($lang ?: '—'); ?></td>
            <td><?php echo esc_html($wkn ?: '—'); ?></td>
            <td><?php echo esc_html($isin ?: '—'); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php
}

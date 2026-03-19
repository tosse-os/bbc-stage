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

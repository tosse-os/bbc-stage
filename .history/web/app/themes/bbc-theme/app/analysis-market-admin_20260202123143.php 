<?php

/**
 * Fügt im Analysis-Menü einen Importpunkt hinzu
 * Nur für Administratoren sichtbar
 */
add_action('admin_menu', function () {

  add_submenu_page(
    'edit.php?post_type=analysis',
    'Markt-Import',
    'Markt-Import',
    'manage_options',
    'analysis-market-import',
    'analysis_market_import_page'
  );

});

/**
 * Rendert die Import-Seite im Admin
 */
function analysis_market_import_page()
{
  ?>
  <div class="wrap">
    <h1>Märkte importieren</h1>

    <p>
      Importiert hierarchische Marktstrukturen (JSON).
      Bestehende Einträge werden anhand ihrer IDs aktualisiert.
    </p>

    <form method="post" enctype="multipart/form-data">
      <?php wp_nonce_field('analysis_market_import', 'analysis_market_import_nonce'); ?>

      <input type="file" name="market_json" accept="application/json" required>

      <p>
        <button class="button button-primary">Import starten</button>
      </p>
    </form>
  </div>
  <?php
}

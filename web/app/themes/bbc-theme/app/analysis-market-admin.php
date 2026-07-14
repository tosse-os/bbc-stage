<?php

add_action('admin_menu', function () {

  $assets_overview_slug = 'edit-tags.php?taxonomy=analysis_market&post_type=analysis';

  remove_submenu_page('edit.php?post_type=analysis', $assets_overview_slug);
  remove_submenu_page('edit.php?post_type=analysis', 'edit-tags.php?taxonomy=analysis_market&amp;post_type=analysis');
  remove_submenu_page('edit.php?post_type=analysis', 'analysis-market-import');
  remove_submenu_page('edit.php?post_type=analysis', 'analysis-import-control');

  add_menu_page(
    'Assets',
    'Assets',
    'edit_posts',
    'analysis-assets',
    'analysis_assets_redirect_to_overview',
    'dashicons-chart-line',
    26
  );

  add_submenu_page(
    'analysis-assets',
    'Overview',
    'Overview',
    'edit_posts',
    $assets_overview_slug,
    ''
  );

  add_submenu_page(
    'analysis-assets',
    'Import New',
    'Import New',
    'manage_options',
    'analysis-market-import',
    'analysis_market_import_page'
  );

  add_submenu_page(
    'analysis-assets',
    'Import-Kontrolle',
    'Import-Kontrolle',
    'manage_options',
    'analysis-import-control',
    'analysis_import_control_view'
  );

  remove_submenu_page('analysis-assets', 'analysis-assets');
}, 999);

function analysis_assets_redirect_to_overview()
{
  wp_safe_redirect(admin_url('edit-tags.php?taxonomy=analysis_market&post_type=analysis'));
  exit;
}

add_filter('parent_file', function ($parent_file) {
  $taxonomy = $_GET['taxonomy'] ?? '';
  $post_type = $_GET['post_type'] ?? '';

  if ($taxonomy === 'analysis_market' && $post_type === 'analysis') {
    return 'analysis-assets';
  }

  if (($GLOBALS['plugin_page'] ?? '') === 'analysis-market-import') {
    return 'analysis-assets';
  }

  if (($GLOBALS['plugin_page'] ?? '') === 'analysis-import-control') {
    return 'analysis-assets';
  }

  return $parent_file;
});

add_filter('submenu_file', function ($submenu_file) {
  $taxonomy = $_GET['taxonomy'] ?? '';
  $post_type = $_GET['post_type'] ?? '';

  if ($taxonomy === 'analysis_market' && $post_type === 'analysis') {
    return 'edit-tags.php?taxonomy=analysis_market&post_type=analysis';
  }

  if (($GLOBALS['plugin_page'] ?? '') === 'analysis-market-import') {
    return 'analysis-market-import';
  }

  if (($GLOBALS['plugin_page'] ?? '') === 'analysis-import-control') {
    return 'analysis-import-control';
  }

  return $submenu_file;
});

function analysis_market_import_page()
{
  if (!current_user_can('manage_options')) {
    wp_die('Nicht erlaubt.');
  }
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
    if (isset($_GET['import']) && $_GET['import'] === 'done') {
      $log = get_transient('analysis_market_import_log');

      if ($log && is_array($log)) {
    ?>
        <h2>Import-Protokoll</h2>
        <pre style="background:#111;color:#0f0;padding:15px;max-height:500px;overflow:auto;"><?php echo esc_html(implode("\n", $log)); ?></pre>
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

function analysis_import_control_view()
{
  if (!current_user_can('manage_options')) {
    wp_die('Nicht erlaubt.');
  }

  $search = isset($_GET['s']) ? trim(sanitize_text_field($_GET['s'])) : '';

  $all_terms = get_terms([
    'taxonomy'   => 'analysis_market',
    'hide_empty' => false,
    'orderby'    => 'parent',
    'order'      => 'ASC',
  ]);

  if (is_wp_error($all_terms)) {
    $all_terms = [];
  }

  $search_lc = mb_strtolower($search);

  $terms = [];

  foreach ($all_terms as $term) {
    if ($search_lc === '') {
      $terms[] = $term;
      continue;
    }

    $name_lc = mb_strtolower($term->name);

    if (strpos($name_lc, $search_lc) !== false) {
      $terms[] = $term;
      continue;
    }

    if ($term->parent) {
      $parent = get_term($term->parent, 'analysis_market');

      if ($parent && !is_wp_error($parent)) {
        if (strpos(mb_strtolower($parent->name), $search_lc) !== false) {
          $terms[] = $term;
        }
      }
    }
  }

  $terms_by_id = [];

  foreach ($terms as $term) {
    $terms_by_id[$term->term_id] = $term;
  }

  if ($search_lc !== '') {
    foreach ($terms as $term) {
      $parent_id = $term->parent;

      while ($parent_id && !isset($terms_by_id[$parent_id])) {
        $parent = get_term($parent_id, 'analysis_market');

        if (!$parent || is_wp_error($parent)) {
          break;
        }

        $terms_by_id[$parent->term_id] = $parent;
        $parent_id = $parent->parent;
      }
    }
  }

  $tree = [];

  foreach ($terms_by_id as $term) {
    $tree[$term->parent][] = $term;
  }
?>
  <div class="wrap">
    <h1>Import-Kontrolle – Analysis Markets</h1>

    <form method="get" style="margin-bottom:12px;">
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
        <?php analysis_import_control_render_rows(0, $tree); ?>
      </tbody>
    </table>
  </div>
  <?php
}

function analysis_import_control_render_rows($parent_id, array $tree, int $level = 0): void
{
  if (!isset($tree[$parent_id])) {
    return;
  }

  foreach ($tree[$parent_id] as $term) {
    $indent = str_repeat('— ', $level);

    $lang = '';

    if (function_exists('pll_get_term_language')) {
      $lang = pll_get_term_language($term->term_id);
    } elseif (function_exists('wpml_get_language_information')) {
      $info = wpml_get_language_information(null, $term->term_id);
      $lang = $info['language_code'] ?? '';
    }

    $wkn = get_term_meta($term->term_id, 'wkn', true);
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
    analysis_import_control_render_rows($term->term_id, $tree, $level + 1);
  }
}

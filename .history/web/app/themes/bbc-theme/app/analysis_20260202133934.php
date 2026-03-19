<?php

/**
 * Custom Post Type: Analysis
 * Wird ausschließlich im Dashboard verwendet (nicht öffentlich).
 * Polylang-kompatibel, ACF-Felder werden angebunden.
 */
add_action('init', function () {
  register_post_type('analysis', [
    'labels' => [
      'name' => __('Analysen'),
      'singular_name' => __('Analyse'),
    ],
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'supports' => ['title'],
    'has_archive' => false,
    'rewrite' => [
      'slug' => 'analysis',
      'with_front' => false,
    ],
    'exclude_from_search' => true,
  ]);
});

/**
 * Taxonomie: Märkte
 * Hierarchische Marktstruktur für Analysen
 */
add_action('init', function () {

  register_taxonomy('analysis_market', ['analysis'], [
    'labels' => [
      'name' => __('Märkte'),
      'singular_name' => __('Markt'),
    ],
    'hierarchical' => true,
    'public' => false,
    'show_ui' => true,
    'show_admin_column' => true,
    'show_in_rest' => false,
    'rewrite' => false,
  ]);
});

/**
 * Setzt bei Auswahl eines Untermarkts automatisch den Übermarkt
 * Verhindert Rekursion und Endlosschleifen beim Setzen von Taxonomie-Terms
 */
add_action('set_object_terms', function ($object_id, $terms, $tt_ids, $taxonomy) {

  if ($taxonomy !== 'analysis_market') {
    return;
  }

  static $running = false;
  if ($running) {
    return;
  }
  $running = true;

  $all_terms = $terms;

  foreach ($terms as $term_id) {
    $term = get_term($term_id, $taxonomy);
    if ($term && $term->parent && !in_array($term->parent, $all_terms, true)) {
      $all_terms[] = $term->parent;
    }
  }

  if ($all_terms !== $terms) {
    wp_set_object_terms($object_id, $all_terms, $taxonomy, false);
  }

  $running = false;
}, 10, 4);

/**
 * Erzwingt im Admin, dass pro Analyse nur ein Markt (Leaf-Term) auswählbar ist
 * Verhindert Mehrfachzuweisungen, die Filter und Auswertungen zerstören würden
 */
add_action('admin_footer-post.php', 'analysis_market_single_term_js');
add_action('admin_footer-post-new.php', 'analysis_market_single_term_js');

function analysis_market_single_term_js()
{
  $screen = get_current_screen();
  if ($screen->post_type !== 'analysis') {
    return;
  }
?>
  <script>
    document.querySelectorAll('#analysis_marketchecklist input[type=checkbox]').forEach(cb => {
      cb.addEventListener('change', function() {
        if (this.checked) {
          document.querySelectorAll('#analysis_marketchecklist input[type=checkbox]').forEach(other => {
            if (other !== this) other.checked = false;
          });
        }
      });
    });
  </script>
<?php
}

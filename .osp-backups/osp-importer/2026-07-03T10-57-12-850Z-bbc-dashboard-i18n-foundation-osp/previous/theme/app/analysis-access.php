<?php

/**
 * Analyse-Zugriffskontrolle
 * Prüft, ob ein User Analysen sehen darf.
 */

function can_view_analysis($user_id)
{
  $state = dashboard_access_state($user_id);
  return in_array($state, ['trial', 'active'], true);
}


/**
 * Speichert beim Speichern einer Analyse einen festen Snapshot
 * von Asset- (Name, WKN, ISIN) und Market-Name als Post-Metadaten.
 */

add_action('save_post_analysis', function ($post_id) {

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;

  $terms = get_the_terms($post_id, 'analysis_market');
  if (empty($terms) || is_wp_error($terms)) return;

  $asset = null;
  $market = null;

  foreach ($terms as $t) {
    if ($t->parent !== 0) $asset = $t;
    else $market = $t;
  }

  if ($asset) {
    update_post_meta($post_id, 'report_asset_name', $asset->name);
    update_post_meta($post_id, 'report_wkn', get_term_meta($asset->term_id, 'wkn', true));
    update_post_meta($post_id, 'report_isin', get_term_meta($asset->term_id, 'isin', true));
  }

  if ($market) {
    update_post_meta($post_id, 'report_market_name', $market->name);
  }
});

add_action('pre_get_posts', function ($query) {

  if (is_admin() || !$query->is_main_query()) {
    return;
  }

  if ($query->get('post_type') !== 'analysis') {
    return;
  }

  if (!can_view_analysis(get_current_user_id())) {
    $query->set('post__in', [0]);
  }
});

add_action('template_redirect', function () {

  if (is_singular('analysis')) {
    if (!can_view_analysis(get_current_user_id())) {
      wp_redirect('/dashboard-payment-required');
      exit;
    }
  }
});

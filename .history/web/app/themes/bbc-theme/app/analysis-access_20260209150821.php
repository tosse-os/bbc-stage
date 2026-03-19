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

add_action('save_post_analysis', function ($post_id) {

  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (wp_is_post_revision($post_id)) return;

  $terms = get_the_terms($post_id, 'analysis_market');
  if (!$terms || is_wp_error($terms)) return;

  $asset = $terms[0];

  $market = $asset->parent
    ? get_term($asset->parent, 'analysis_market')
    : null;

  update_post_meta($post_id, 'report_asset_name',  $asset->name);
  update_post_meta($post_id, 'report_market_name', $market ? $market->name : null);
  update_post_meta($post_id, 'report_wkn',          get_term_meta($asset->term_id, 'wkn', true));
  update_post_meta($post_id, 'report_isin',         get_term_meta($asset->term_id, 'isin', true));
}, 20);

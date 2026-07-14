<?php

/**
 * Registriert Footer- und Dashboard-Texte für Polylang.
 */
add_action('init', function () {

  /* Footer */
  pll_register_string('footer_follow', 'Follow Us', 'Footer');
  pll_register_string('footer_about', 'About', 'Footer');
  pll_register_string('footer_services', 'Services', 'Footer');
  pll_register_string('footer_contact', 'Contact', 'Footer');
  pll_register_string('footer_privacy', 'Privacy policy', 'Footer');
  pll_register_string('footer_imprint', 'Imprint', 'Footer');

  /* Dashboard */
  pll_register_string('dashboard_copy', 'Kopieren', 'Dashboard');
  pll_register_string('dashboard_copied', 'Kopiert!', 'Dashboard');
});

add_action('init', function () {
    if (! function_exists('pll_register_string')) {
        return;
    }

    foreach (
        [
            'Customer Reviews',
            'Voices of Innovation',
            'Previous review',
            'Next review',
            'Read more',
            'Show less',
            'Reviews',
            'Premium Content',
            'Kostenlose Analysen sichern',
        ] as $string
    ) {
        pll_register_string($string, $string, 'Landing Page');
    }
});

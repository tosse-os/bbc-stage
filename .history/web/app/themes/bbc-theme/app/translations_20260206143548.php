<?php

/**
* Registriert Footer-Texte für Polylang.
*/
add_action('init', function () {
pll_register_string('footer_follow', 'Follow Us', 'Footer');
pll_register_string('footer_about', 'About', 'Footer');
pll_register_string('footer_services', 'Services', 'Footer');
pll_register_string('footer_contact', 'Contact', 'Footer');
pll_register_string('footer_privacy', 'Privacy policy', 'Footer');
pll_register_string('footer_imprint', 'Imprint', 'Footer');
});

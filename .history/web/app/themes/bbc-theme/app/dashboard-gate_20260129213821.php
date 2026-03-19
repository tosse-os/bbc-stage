<?php

/**
 * Dashboard-Gate – DEBUG VERSION
 * Gibt ALLE relevanten Zustände direkt im Browser aus
 */

add_action('template_redirect', function () {

  header('Content-Type: text/plain; charset=utf-8');

  echo "=== DASHBOARD GATE DEBUG ===\n\n";

  echo "REQUEST_URI:\n";
  echo $_SERVER['REQUEST_URI'] . "\n\n";

  echo "is_page():\n";
  var_dump(is_page());
  echo "\n\n";

  echo "is_user_logged_in():\n";
  var_dump(is_user_logged_in());
  echo "\n\n";

  $template = get_page_template_slug();

  echo "get_page_template_slug():\n";
  var_dump($template);
  echo "\n\n";

  echo "Page ID:\n";
  var_dump(get_queried_object_id());
  echo "\n\n";

  echo "Page title:\n";
  echo get_the_title() . "\n\n";

  echo "---- CHECKS ----\n\n";

  if (is_user_logged_in() && in_array($template, [
    'page-dashboard-login.blade.php',
    'page-dashboard-register.blade.php',
  ], true)) {

    echo "MATCH: eingeloggt + LOGIN/REGISTER TEMPLATE\n";
    echo "→ würde redirecten nach /dashboard\n";
    exit;
  }

  if (in_array($template, [
    'page-dashboard-login.blade.php',
    'page-dashboard-register.blade.php',
  ], true)) {

    echo "MATCH: LOGIN/REGISTER TEMPLATE (Gast erlaubt)\n";
    exit;
  }

  if ($template !== 'page-dashboard.blade.php') {
    echo "MATCH: NICHT Dashboard-Template\n";
    exit;
  }

  if (!is_user_logged_in()) {
    echo "MATCH: Gast auf Dashboard\n";
    echo "→ würde redirecten nach /dashboard-login\n";
    exit;
  }

  echo "MATCH: Eingeloggt + Dashboard → OK\n";
  exit;
});

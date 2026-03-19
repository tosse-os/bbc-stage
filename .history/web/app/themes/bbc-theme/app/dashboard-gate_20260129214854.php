<?php

add_action('template_redirect', function () {

  error_log('--- DASHBOARD GATE ---');

  error_log('REQUEST_URI: ' . $_SERVER['REQUEST_URI']);
  error_log('is_page: ' . (is_page() ? 'true' : 'false'));
  error_log('is_user_logged_in: ' . (is_user_logged_in() ? 'true' : 'false'));

  $template = get_page_template_slug();
  error_log('page_template_slug: ' . ($template ?: '[EMPTY]'));

  $isLogin     = is_page_template('page-dashboard-login.blade.php');
  $isRegister  = is_page_template('page-dashboard-register.blade.php');
  $isDashboard = is_page_template('page-dashboard.blade.php');

  error_log('isLogin: ' . ($isLogin ? 'true' : 'false'));
  error_log('isRegister: ' . ($isRegister ? 'true' : 'false'));
  error_log('isDashboard: ' . ($isDashboard ? 'true' : 'false'));

  // Eingeloggt → Login verbieten
  if (is_user_logged_in() && ($isLogin || $isRegister)) {
    error_log('REDIRECT → /dashboard');
    wp_redirect('/dashboard');
    exit;
  }

  // Gast → Dashboard verbieten
  if (!is_user_logged_in() && $isDashboard) {
    error_log('REDIRECT → /dashboard-login');
    wp_redirect('/dashboard-login');
    exit;
  }

  error_log('NO REDIRECT');
});

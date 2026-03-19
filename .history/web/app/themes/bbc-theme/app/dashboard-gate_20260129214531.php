<?php

add_action('template_redirect', function () {

  if (!is_page()) {
    return;
  }

  $isLogin    = is_page_template('page-dashboard-login.blade.php');
  $isRegister = is_page_template('page-dashboard-register.blade.php');
  $isDashboard = is_page_template('page-dashboard.blade.php');

  // Eingeloggt → Login & Register verbieten
  if (is_user_logged_in() && ($isLogin || $isRegister)) {
    wp_redirect('/dashboard');
    exit;
  }

  // Gast → Dashboard verbieten
  if (!is_user_logged_in() && $isDashboard) {
    wp_redirect('/dashboard-login');
    exit;
  }
});

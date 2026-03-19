<?php

add_action('template_redirect', function () {

  if (!is_page()) {
    return;
  }

  $post = get_queried_object();
  if (!$post) {
    return;
  }

  $slug = $post->post_name;

  #error_log('--- DASHBOARD GATE ---');
  #error_log('SLUG: ' . $slug);
  #error_log('is_user_logged_in: ' . (is_user_logged_in() ? 'true' : 'false'));

  $isLogin     = $slug === 'dashboard-login';
  $isRegister  = $slug === 'dashboard-register';
  $isDashboard = $slug === 'dashboard';
  $isPodcasts  = $slug === 'dashboard-podcasts';

  $isProtectedDashboardArea = $isDashboard || $isPodcasts;

  #error_log('isLogin: ' . ($isLogin ? 'true' : 'false'));
  #error_log('isRegister: ' . ($isRegister ? 'true' : 'false'));
  #error_log('isDashboard: ' . ($isDashboard ? 'true' : 'false'));
  #error_log('isPodcasts: ' . ($isPodcasts ? 'true' : 'false'));

  if (is_user_logged_in() && ($isLogin || $isRegister)) {
    # error_log('REDIRECT -> /dashboard');
    wp_redirect('/dashboard');
    exit;
  }

  if (!is_user_logged_in() && $isProtectedDashboardArea) {
    # error_log('REDIRECT -> /dashboard-login');
    wp_redirect('/dashboard-login');
    exit;
  }

  # error_log('NO REDIRECT');
}, 99);

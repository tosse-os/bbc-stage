<?php

/**
 * Dashboard Zugriffsgate
 * Schützt alle Dashboard-Seiten vor unautorisiertem Zugriff.
 */
add_action('template_redirect', function () {

  if (!is_page()) {
    return;
  }

  $post = get_queried_object();
  if (!$post) {
    return;
  }

  $slug = $post->post_name;

  $isLogin     = $slug === 'dashboard-login';
  $isRegister  = $slug === 'dashboard-register';

  $isDashboard = $slug === 'dashboard';
  $isReports   = $slug === 'dashboard-reports';
  $isMedia     = $slug === 'dashboard-media';
  $isSettings  = $slug === 'dashboard-settings';

  $isProtectedDashboardArea =
    $isDashboard ||
    $isReports ||
    $isMedia ||
    $isSettings;

  if (is_user_logged_in() && ($isLogin || $isRegister)) {
    wp_redirect('/dashboard');
    exit;
  }

  if (!is_user_logged_in() && $isProtectedDashboardArea) {
    wp_redirect('/dashboard-login');
    exit;
  }
}, 99);

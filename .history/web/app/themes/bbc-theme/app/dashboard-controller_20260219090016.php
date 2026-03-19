<?php

/**
 * Dashboard Startlogik
 * Entscheidet, welche View auf /dashboard gerendert wird.
 */

/**
 * Bestimmt die korrekte Dashboard-View anhand des Slugs
 */
// function dashboard_start_view()
// {
//   $state = dashboard_access_state(get_current_user_id());

//   if ($state === 'payment_required') {
//     return 'dashboard.partials.payment-required';
//   }

//   global $post;

//   if (!$post) {
//     return 'dashboard.overview';
//   }

//   switch ($post->post_name) {

//     case 'dashboard':
//       return 'dashboard.overview';

//     case 'dashboard-reports':
//       return 'dashboard.analyses.index';

//     case 'dashboard-media':
//       return 'dashboard.media-entry.index';

//     case 'dashboard-settings':
//       return 'dashboard.settings.index';

//     default:
//       return 'dashboard.overview';
//   }
// }

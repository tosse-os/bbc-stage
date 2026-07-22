<?php

add_action('wp_login', function ($userLogin, $user): void {
  if ($user instanceof WP_User) {
    update_user_meta($user->ID, 'dashboard_last_login_at', current_time('mysql', true));
  }
}, 10, 2);

function bbc_subscriber_admin_plan(int $userId): string
{
  $plan = sanitize_key((string) get_user_meta($userId, 'dashboard_selected_plan', true));

  return in_array($plan, ['trial', 'basis', 'pro'], true) ? $plan : '';
}

function bbc_subscriber_admin_status(int $userId): string
{
  $stripeStatus = sanitize_key((string) get_user_meta($userId, 'stripe_subscription_status', true));

  if ($stripeStatus !== '') {
    return $stripeStatus;
  }

  if (defined('USER_META_SUB_STATUS')) {
    return sanitize_key((string) get_user_meta($userId, USER_META_SUB_STATUS, true));
  }

  return '';
}

function bbc_subscriber_admin_plan_label(string $plan): string
{
  return match ($plan) {
    'trial' => 'Trial',
    'basis' => 'Basis',
    'pro' => 'Pro',
    default => '—',
  };
}

function bbc_subscriber_admin_status_label(string $status): string
{
  return match ($status) {
    'trial', 'trialing' => 'Testphase',
    'active' => 'Aktiv',
    'past_due' => 'Zahlung offen',
    'unpaid' => 'Unbezahlt',
    'incomplete' => 'Unvollständig',
    'incomplete_expired' => 'Abgelaufen',
    'payment_required' => 'Zahlung erforderlich',
    'canceled', 'cancelled' => 'Gekündigt',
    default => $status !== '' ? ucwords(str_replace('_', ' ', $status)) : '—',
  };
}

function bbc_subscriber_admin_datetime($value): string
{
  if ($value === '' || $value === null || $value === false) {
    return '—';
  }

  if (is_numeric($value)) {
    $timestamp = (int) $value;
  } else {
    $timestamp = strtotime((string) $value . ' UTC');
  }

  if (!$timestamp) {
    return '—';
  }

  return wp_date('d.m.Y H:i', $timestamp, wp_timezone());
}

function bbc_subscriber_admin_badge(string $label, string $type): string
{
  $class = sanitize_html_class($type !== '' ? $type : 'empty');

  return '<span class="bbc-subscriber-badge bbc-subscriber-badge--' . esc_attr($class) . '">' . esc_html($label) . '</span>';
}

add_filter('manage_users_columns', function (array $columns): array {
  $columns['bbc_plan'] = 'Tarif';
  $columns['bbc_subscription_status'] = 'Abo-Status';
  $columns['bbc_registered'] = 'Registriert';

  return $columns;
});

add_filter('manage_users_custom_column', function ($output, string $columnName, int $userId): string {
  if ($columnName === 'bbc_plan') {
    $plan = bbc_subscriber_admin_plan($userId);
    return bbc_subscriber_admin_badge(bbc_subscriber_admin_plan_label($plan), $plan);
  }

  if ($columnName === 'bbc_subscription_status') {
    $status = bbc_subscriber_admin_status($userId);
    return bbc_subscriber_admin_badge(bbc_subscriber_admin_status_label($status), $status);
  }


  if ($columnName === 'bbc_registered') {
    $user = get_userdata($userId);
    return $user ? esc_html(bbc_subscriber_admin_datetime($user->user_registered)) : '—';
  }

  return (string) $output;
}, 10, 3);

add_filter('manage_users_sortable_columns', function (array $columns): array {
  $columns['bbc_registered'] = 'registered';

  return $columns;
});

add_action('pre_get_users', function (WP_User_Query $query): void {
  if (!is_admin() || !current_user_can('manage_options')) {
    return;
  }

  $screen = function_exists('get_current_screen') ? get_current_screen() : null;
  if (!$screen || $screen->id !== 'users') {
    return;
  }

  $plan = isset($_GET['bbc_plan']) ? sanitize_key(wp_unslash($_GET['bbc_plan'])) : '';
  $status = isset($_GET['bbc_status']) ? sanitize_key(wp_unslash($_GET['bbc_status'])) : '';
  $metaQuery = (array) $query->get('meta_query');

  if (in_array($plan, ['trial', 'basis', 'pro'], true)) {
    $metaQuery[] = [
      'key' => 'dashboard_selected_plan',
      'value' => $plan,
    ];
  }

  if ($status !== '') {
    $metaQuery[] = [
      'relation' => 'OR',
      [
        'key' => 'stripe_subscription_status',
        'value' => $status,
      ],
      [
        'key' => defined('USER_META_SUB_STATUS') ? USER_META_SUB_STATUS : 'dashboard_subscription_status',
        'value' => $status,
      ],
    ];
  }

  if ($metaQuery !== []) {
    $query->set('meta_query', $metaQuery);
  }

});

add_action('restrict_manage_users', function (): void {
  if (!current_user_can('manage_options')) {
    return;
  }

  $selectedPlan = isset($_GET['bbc_plan']) ? sanitize_key(wp_unslash($_GET['bbc_plan'])) : '';
  $selectedStatus = isset($_GET['bbc_status']) ? sanitize_key(wp_unslash($_GET['bbc_status'])) : '';

  echo '<select name="bbc_plan">';
  echo '<option value="">Alle Tarife</option>';
  foreach (['trial' => 'Trial', 'basis' => 'Basis', 'pro' => 'Pro'] as $value => $label) {
    echo '<option value="' . esc_attr($value) . '" ' . selected($selectedPlan, $value, false) . '>' . esc_html($label) . '</option>';
  }
  echo '</select>';

  echo '<select name="bbc_status">';
  echo '<option value="">Alle Abo-Status</option>';
  foreach (['trialing', 'active', 'past_due', 'unpaid', 'incomplete', 'payment_required', 'canceled'] as $value) {
    echo '<option value="' . esc_attr($value) . '" ' . selected($selectedStatus, $value, false) . '>' . esc_html(bbc_subscriber_admin_status_label($value)) . '</option>';
  }
  echo '</select>';
});

add_action('admin_menu', function (): void {
  add_users_page(
    'Abonnenten',
    'Abonnenten',
    'manage_options',
    'bbc-subscribers',
    'bbc_subscriber_admin_render_page'
  );
});

add_action('admin_head-users.php', 'bbc_subscriber_admin_styles');
add_action('admin_head-users_page_bbc-subscribers', 'bbc_subscriber_admin_styles');

function bbc_subscriber_admin_styles(): void
{
  echo '<style>
    .bbc-subscriber-badge{display:inline-flex;align-items:center;min-height:22px;padding:0 8px;border-radius:999px;background:#f0f0f1;color:#1d2327;font-size:12px;font-weight:600;line-height:1.4;white-space:nowrap}
    .bbc-subscriber-badge--active{background:#dff4e4;color:#14532d}
    .bbc-subscriber-badge--trial,.bbc-subscriber-badge--trialing{background:#dbeafe;color:#1e3a8a}
    .bbc-subscriber-badge--basis{background:#e0f2fe;color:#0c4a6e}
    .bbc-subscriber-badge--pro{background:#ede9fe;color:#4c1d95}
    .bbc-subscriber-badge--past_due,.bbc-subscriber-badge--unpaid,.bbc-subscriber-badge--payment_required,.bbc-subscriber-badge--incomplete{background:#fef3c7;color:#78350f}
    .bbc-subscriber-badge--canceled,.bbc-subscriber-badge--cancelled,.bbc-subscriber-badge--incomplete_expired{background:#fee2e2;color:#7f1d1d}
    .bbc-subscriber-metrics{display:grid;grid-template-columns:repeat(6,minmax(130px,1fr));gap:12px;margin:18px 0}
    .bbc-subscriber-metric{padding:16px;border:1px solid #dcdcde;background:#fff}
    .bbc-subscriber-metric strong{display:block;margin-top:5px;font-size:24px;line-height:1.15}
    .bbc-subscriber-table .column-user{width:24%}
    .bbc-subscriber-table .column-plan,.bbc-subscriber-table .column-status{width:11%}
    .bbc-subscriber-table .column-actions{width:105px;text-align:right}
    .users-php .wp-list-table .column-role{width:90px!important}
    @media screen and (max-width:1200px){.bbc-subscriber-metrics{grid-template-columns:repeat(3,minmax(130px,1fr))}}
    @media screen and (max-width:782px){.bbc-subscriber-metrics{grid-template-columns:1fr 1fr}}
  </style>';
}

function bbc_subscriber_admin_get_ids(): array
{
  return array_map('intval', get_users([
    'role' => 'subscriber',
    'fields' => 'ids',
    'number' => -1,
  ]));
}

function bbc_subscriber_admin_metrics(): array
{
  $metrics = [
    'total' => 0,
    'trial' => 0,
    'basis' => 0,
    'pro' => 0,
    'active' => 0,
    'issues' => 0,
  ];

  foreach (bbc_subscriber_admin_get_ids() as $userId) {
    $metrics['total']++;
    $plan = bbc_subscriber_admin_plan($userId);
    $status = bbc_subscriber_admin_status($userId);

    if (isset($metrics[$plan])) {
      $metrics[$plan]++;
    }

    if (in_array($status, ['active', 'trialing', 'trial'], true)) {
      $metrics['active']++;
    }

    if (in_array($status, ['past_due', 'unpaid', 'incomplete', 'incomplete_expired', 'payment_required'], true)) {
      $metrics['issues']++;
    }
  }

  return $metrics;
}

function bbc_subscriber_admin_render_page(): void
{
  if (!current_user_can('manage_options')) {
    wp_die('Keine Berechtigung.');
  }

  $table = new BBC_Subscriber_Admin_Table();
  $table->prepare_items();
  $metrics = bbc_subscriber_admin_metrics();

  echo '<div class="wrap">';
  echo '<h1 class="wp-heading-inline">Abonnenten</h1>';
  echo '<hr class="wp-header-end">';
  echo '<div class="bbc-subscriber-metrics">';

  foreach ([
    'total' => 'Abonnenten',
    'active' => 'Aktiv / Trial',
    'trial' => 'Trial',
    'basis' => 'Basis',
    'pro' => 'Pro',
    'issues' => 'Zahlungsprobleme',
  ] as $key => $label) {
    echo '<div class="bbc-subscriber-metric"><span>' . esc_html($label) . '</span><strong>' . esc_html((string) $metrics[$key]) . '</strong></div>';
  }

  echo '</div>';
  echo '<form method="get">';
  echo '<input type="hidden" name="page" value="bbc-subscribers">';
  $table->search_box('Abonnenten durchsuchen', 'bbc-subscriber-search');
  $table->display();
  echo '</form>';
  echo '</div>';
}

if (!class_exists('WP_List_Table')) {
  require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class BBC_Subscriber_Admin_Table extends WP_List_Table
{
  public function __construct()
  {
    parent::__construct([
      'singular' => 'Abonnent',
      'plural' => 'Abonnenten',
      'ajax' => false,
    ]);
  }

  public function get_columns(): array
  {
    return [
      'user' => 'Benutzer',
      'plan' => 'Tarif',
      'status' => 'Abo-Status',
      'registered' => 'Registriert',
      'last_login' => 'Letzter Login',
      'period_end' => 'Verlängerung / Ende',
      'actions' => '',
    ];
  }

  protected function get_sortable_columns(): array
  {
    return [
      'user' => ['display_name', false],
      'registered' => ['registered', false],
      'last_login' => ['last_login', false],
      'period_end' => ['period_end', false],
    ];
  }

  protected function extra_tablenav($which): void
  {
    if ($which !== 'top') {
      return;
    }

    $selectedPlan = isset($_GET['plan']) ? sanitize_key(wp_unslash($_GET['plan'])) : '';
    $selectedStatus = isset($_GET['status']) ? sanitize_key(wp_unslash($_GET['status'])) : '';

    echo '<div class="alignleft actions">';
    echo '<select name="plan"><option value="">Alle Tarife</option>';
    foreach (['trial' => 'Trial', 'basis' => 'Basis', 'pro' => 'Pro'] as $value => $label) {
      echo '<option value="' . esc_attr($value) . '" ' . selected($selectedPlan, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
    echo '<select name="status"><option value="">Alle Abo-Status</option>';
    foreach (['trialing', 'active', 'past_due', 'unpaid', 'incomplete', 'payment_required', 'canceled'] as $value) {
      echo '<option value="' . esc_attr($value) . '" ' . selected($selectedStatus, $value, false) . '>' . esc_html(bbc_subscriber_admin_status_label($value)) . '</option>';
    }
    echo '</select>';
    submit_button('Filtern', '', 'filter_action', false);
    echo '</div>';
  }

  public function no_items(): void
  {
    echo 'Keine Abonnenten gefunden.';
  }

  public function prepare_items(): void
  {
    $perPage = 25;
    $page = $this->get_pagenum();
    $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $plan = isset($_GET['plan']) ? sanitize_key(wp_unslash($_GET['plan'])) : '';
    $status = isset($_GET['status']) ? sanitize_key(wp_unslash($_GET['status'])) : '';
    $orderBy = isset($_GET['orderby']) ? sanitize_key(wp_unslash($_GET['orderby'])) : 'registered';
    $order = isset($_GET['order']) && strtolower((string) $_GET['order']) === 'asc' ? 'ASC' : 'DESC';

    $args = [
      'role' => 'subscriber',
      'number' => $perPage,
      'offset' => ($page - 1) * $perPage,
      'count_total' => true,
      'order' => $order,
      'orderby' => $orderBy === 'display_name' ? 'display_name' : ($orderBy === 'registered' ? 'registered' : 'meta_value_num'),
    ];

    if ($search !== '') {
      $args['search'] = '*' . $search . '*';
      $args['search_columns'] = ['user_login', 'user_email', 'display_name'];
    }

    $metaQuery = [];

    if (in_array($plan, ['trial', 'basis', 'pro'], true)) {
      $metaQuery[] = [
        'key' => 'dashboard_selected_plan',
        'value' => $plan,
      ];
    }

    if ($status !== '') {
      $metaQuery[] = [
        'relation' => 'OR',
        [
          'key' => 'stripe_subscription_status',
          'value' => $status,
        ],
        [
          'key' => defined('USER_META_SUB_STATUS') ? USER_META_SUB_STATUS : 'dashboard_subscription_status',
          'value' => $status,
        ],
      ];
    }

    if ($orderBy === 'last_login') {
      $args['meta_key'] = 'dashboard_last_login_at';
      $args['orderby'] = 'meta_value';
    } elseif ($orderBy === 'period_end') {
      $args['meta_key'] = 'stripe_current_period_end';
    }

    if ($metaQuery !== []) {
      $args['meta_query'] = $metaQuery;
    }

    $query = new WP_User_Query($args);
    $this->items = $query->get_results();
    $this->_column_headers = [$this->get_columns(), [], $this->get_sortable_columns()];
    $this->set_pagination_args([
      'total_items' => (int) $query->get_total(),
      'per_page' => $perPage,
      'total_pages' => (int) ceil($query->get_total() / $perPage),
    ]);
  }

  protected function column_default($item, $columnName): string
  {
    $userId = (int) $item->ID;

    if ($columnName === 'plan') {
      $plan = bbc_subscriber_admin_plan($userId);
      return bbc_subscriber_admin_badge(bbc_subscriber_admin_plan_label($plan), $plan);
    }

    if ($columnName === 'status') {
      $status = bbc_subscriber_admin_status($userId);
      return bbc_subscriber_admin_badge(bbc_subscriber_admin_status_label($status), $status);
    }

    if ($columnName === 'registered') {
      return esc_html(bbc_subscriber_admin_datetime($item->user_registered));
    }

    if ($columnName === 'last_login') {
      return esc_html(bbc_subscriber_admin_datetime(get_user_meta($userId, 'dashboard_last_login_at', true)));
    }

    if ($columnName === 'period_end') {
      return esc_html(bbc_subscriber_admin_datetime(get_user_meta($userId, 'stripe_current_period_end', true)));
    }

    if ($columnName === 'actions') {
      return '<a class="button button-small" href="' . esc_url(get_edit_user_link($userId)) . '">Öffnen</a>';
    }

    return '—';
  }

  protected function column_user($item): string
  {
    $editUrl = get_edit_user_link((int) $item->ID);
    $name = $item->display_name !== '' ? $item->display_name : $item->user_login;

    return '<strong><a href="' . esc_url($editUrl) . '">' . esc_html($name) . '</a></strong><br><a href="mailto:' . esc_attr($item->user_email) . '">' . esc_html($item->user_email) . '</a>';
  }
}
